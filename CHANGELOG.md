
CHANGELOG for 0.1.x
===================

This changelog references the relevant changes (bug and security fixes) done
in 0.1 minor versions.

To get the diff for a specific change, go to https://github.com/superdesk/web-publisher/commit/XXX where XXX is the change hash

* 0.1.0-8
 * bug/feature/maintenance [#165] Switch to ORM as main storage backend 

* 0.1.0-7

 * maintenance [#156] added memcached to project requirements and configured it as default sessions handler
 * maintenance [#156] Specified project requirements
 * feature [#155] Upgrade Symfony version to 3.1
 * feature [#153] Bump lowest PHP version to ^7.0
 * bug [#152] [ContentBundle] If I correct a headline, it and its article are published in addition to the original (takeit)

* 0.1.0-6

 * feature [#138] [RuleBundle][Rule][ContentBundle] Added content to route mapping with simple rules managed by API (takeit)
 * feature [#139] Add default templates for error pages.
 * feature [#128] [ContentBundle] Add route type constraint validator (takeit)
 * bug [#128] [ContentBundle] Improve possibility to un-assign/assign parent routes (takeit)
 * feature [#132] [Template System][TemplateEngine Bundle] implement pagination in gimmelist

* 0.1.0-5 

 * bug [#128] [ContentBundle] Un-assigning content from route doesn't work (takeit)
 * feature [#128] [ContentBundle] Allow to assign/un-assign route to article (takeit)
 * bug [#129] [CoreBundle][ContentBundle] Change template name discovery, add articles_template_name to route (see updated documentation)
 * bug [#123] [ContentBundle][Bridge] Article's body is not pre-filled (takeit)
 * bug [#122] [Templates System] add custom cache key generator for meta objects
 * feature [#120] Make routes of "collection" type accessible (takeit)
 * bug [#120] Can't assign content to route of type "collection" (takeit)
 * feature [#115] Allow to read/write article metadata based on provided package (takeit)
 * bug [#108] Set current route to context {{ gimme.route }} (djbrd-sourcefabric, ahilles107)
 * feature [#105] Make Meta context aware - every property inside Meta class will be converted to Meta if config for it will be registered (ahilles107)
 * feature [#105] Implement Article Media handling - handle images coming with packages and items  (ahilles107)