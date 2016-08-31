
CHANGELOG for 0.1.x
===================

This changelog references the relevant changes (bug and security fixes) done
in 0.1 minor versions.

To get the diff for a specific change, go to https://github.com/superdesk/web-publisher/commit/XXX where XXX is the change hash

* 0.1.0-5 

 * bug [#123] [ContentBundle][Bridge] Article's body is not pre-filled (takeit)
 * feature [#120] Make routes of "collection" type accessible (takeit)
 * bug [#120] Can't assign content to route of type "collection" (takeit)
 * feature [#115] Allow to read/write article metadata based on provided package (takeit)
 * fix [#108] Set current route to context {{ gimme.route }} (djbrd-sourcefabric, ahilles107)
 * feature [#105] Make Meta context aware - every property inside Meta class will be converted to Meta if config for it will be registered (ahilles107)
 * feature [#105] Implement Article Media handling - handle images coming with packages and items  (ahilles107)