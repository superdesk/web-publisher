Superdesk Content API Bridge bundle
==========

Configuration options

```yml
    superdeskbridge:
        protocol: http
        host: publicapi
        port: 5050
```

Via the options parameter its possible to set default options / headers for 
your http client. They will be send as the third parameter for the method
makeApiCall in the ClientInterface.

E.g.

```yml
    superdeskbridge:
        options:
            curl:
                10203: # CURLOPT_RESOLVE
                 - "example.com:5050:localhost"  # This will resolve the host example.com to your localhost 
```
