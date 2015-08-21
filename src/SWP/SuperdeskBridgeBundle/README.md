Superdesk Content API Bridge bundle
======

Configuration options
------

Option | Documentation
--- | ---
swp_superdesk_bridge.base_uri | Fill in the address for your content api instance 


Example Configuration
------

```yml
    swp_superdesk_bridge.base_uri: 'http://localhost:5050' 
```

Via the options parameter its possible to set default options / headers for 
your http client. They will be send as the third parameter for the method
makeApiCall in the ClientInterface.

Development Configuration
------

This example is specific for the Guzzle client and allows you to do custom 
hostname resolving, practical for you dev environment.

E.g.

```yml
    swp_superdesk_bridge.base_uri: http://publicapi:5050
    swp_superdesk_bridge.options:
            curl:
                10203: # CURLOPT_RESOLVE
                 - "publicapi:5050:localhost"  # This will resolve the host publicapi to your localhost 
```
