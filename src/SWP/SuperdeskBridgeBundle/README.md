Superdesk Content API Bridge bundle
==========

Configuration options

```yml
    swp_superdesk_bridge.base_uri: 'http://localhost:5050'
```

Via the options parameter its possible to set default options / headers for 
your http client. They will be send as the third parameter for the method
makeApiCall in the ClientInterface.

E.g.

```yml
    swp_superdesk_bridge.base_uri: http://publicapi:5050
    swp_superdesk_bridge.options:
            curl:
                10203: # CURLOPT_RESOLVE
                 - "publicapi:5050:localhost"  # This will resolve the host publicapi to your localhost 
```
