vcl 4.0;
import xkey;

include "devicedetect.vcl";

# Default backend definition. Set this to point to your content server.
backend default {
    .host = "127.0.0.1";
    .port = "80";
}

acl invalidators {
    "localhost";
    "127.0.0.1"/24;
}

acl profile {
   "127.0.0.1";
}

sub vcl_recv {
    # Happens before we check if we have this in cache already.
    #
    # Typically you clean up the request here, removing cookies you don't need,
    # rewriting the request, etc.

    call devicedetect;

    # allow cache miss
    if (req.http.Cache-Control ~ "no-cache" && client.ip ~ invalidators) {
        set req.hash_always_miss = true;
    }

    # Uncomment it when site requires authorization header (ex. staging)
    # by default those requests are not cached

    # if (req.method == "GET" && !req.url ~ "api") {
    #    unset req.http.Authorization;
    # }

    # allow PURGE
    if (req.method == "PURGE") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed"));
        }
        return (purge);
    }

    # allow PURGEKEYS
    if (req.method == "PURGEKEYS") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed"));
        }

        # If neither of the headers are provided we return 400 to simplify detecting wrong configuration
        if (!req.http.xkey-purge && !req.http.xkey-softpurge) {
            return (synth(400, "Neither header XKey-Purge or XKey-SoftPurge set"));
        }

        # Based on provided header invalidate (purge) and/or expire (softpurge) the tagged content
        set req.http.n-gone = 0;
        set req.http.n-softgone = 0;
        if (req.http.xkey-purge) {
            set req.http.n-gone = xkey.purge(req.http.xkey-purge);
        }

        if (req.http.xkey-softpurge) {
            set req.http.n-softgone = xkey.softpurge(req.http.xkey-softpurge);
        }

        return (synth(200, "Purged "+req.http.n-gone+" objects, expired "+req.http.n-softgone+" objects"));
    }

    # allow ban
    if (req.method == "BAN") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed"));
        }

        if (req.http.X-Cache-Tags) {
            ban("obj.http.X-Host ~ " + req.http.X-Host
                + " && obj.http.X-Url ~ " + req.http.X-Url
                + " && obj.http.content-type ~ " + req.http.X-Content-Type
                + " && obj.http.X-Cache-Tags ~ " + req.http.X-Cache-Tags
            );
        } else {
            ban("obj.http.X-Host ~ " + req.http.X-Host
                + " && obj.http.X-Url ~ " + req.http.X-Url
                + " && obj.http.content-type ~ " + req.http.X-Content-Type
            );
        }

        return (synth(200, "Banned"));
    }

    if (req.method != "GET" && req.method != "HEAD") {
      return (pass);
    }

    // reverse proxy protocol handling
    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }

    // reverse proxy ip handling
    if (req.http.cf-connecting-ip) {
        set req.http.X-Forwarded-For = req.http.cf-connecting-ip;
    } else {
        set req.http.X-Forwarded-For = client.ip;
    }

    // Remove all cookies except the session ID.
    if (req.http.Cookie) {
        set req.http.Cookie = ";" + req.http.Cookie;
        set req.http.Cookie = regsuball(req.http.Cookie, "; +", ";");
        set req.http.Cookie = regsuball(req.http.Cookie, ";(SUPERDESKPUBLISHER|REMEMBERME)=", "; \1=");
        set req.http.Cookie = regsuball(req.http.Cookie, ";[^ ][^;]*", "");
        set req.http.Cookie = regsuball(req.http.Cookie, "^[; ]+|[; ]+$", "");

        if (req.http.Cookie == "") {
            // If there are no more cookies, remove the header to get page cached.
            unset req.http.Cookie;
        }
    }

    // allow blackfire.io profiles to reach the server real responses
    if (req.http.X-Blackfire-Query && client.ip ~ profile) {
      return (pass);
    } else {
      // Add a Surrogate-Capability header to announce ESI support.
      set req.http.Surrogate-Capability = "abc=ESI/1.0";
    }
}


sub vcl_backend_response {
    # Happens after we have read the response headers from the backend.
    #
    # Here you clean the response headers, removing silly Set-Cookie headers
    # and other mistakes your backend does.

    if (bereq.http.X-UA-Device) {
        if (!beresp.http.Vary) { # no Vary at all
            set beresp.http.Vary = "X-UA-Device";
        } elseif (beresp.http.Vary !~ "X-UA-Device") {
            set beresp.http.Vary = beresp.http.Vary + ", X-UA-Device";
        }
    }
    set beresp.http.X-UA-Device = bereq.http.X-UA-Device;

    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }

    set beresp.http.X-Url = bereq.url;
    set beresp.http.X-Host = bereq.http.host;
}

sub vcl_deliver {
    # Happens when we have all the pieces we need, and are about to send the
    # response to the client.
    #
    # You can do accounting or modifying the final object here.

    if (req.http.X-UA-Device && resp.http.Vary) {
        set resp.http.Vary = regsub(resp.http.Vary, "X-UA-Device", "User-Agent");
    }

    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
        set resp.http.X-Cache-Hits = obj.hits;
    } else {
        set resp.http.X-Cache = "MISS";
    }

    // x-cache-debug is added when publisher is in debug mode
    if (!resp.http.X-Cache-Debug) {
        # Remove ban-lurker friendly custom headers when delivering to client
        unset resp.http.X-Url;
        unset resp.http.X-Host;
        unset resp.http.xkey;
        unset resp.http.X-UA-Device;

        # Unset the tagged cache headers
        unset resp.http.X-Cache-Tags;
    }

    if (req.method == "OPTIONS") {
        set resp.http.Access-Control-Max-Age = "1728000";
        set resp.http.Access-Control-Allow-Methods = "GET, POST, PUT, DELETE, PATCH, OPTIONS";
        set resp.http.Access-Control-Allow-Headers = "Authorization,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,Keep-Alive,X-Requested-With,If-Modified-Since";

        set resp.http.Content-Length = "0";
        set resp.http.Content-Type = "text/plain charset=UTF-8";
        set resp.status = 204;
    }
}
