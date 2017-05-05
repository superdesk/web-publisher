
CHANGELOG for version <= 0.6.x
==============================

This changelog references the relevant changes (bug and security fixes) done
in <= 0.6 minor versions.

To get the diff for a specific change, go to https://github.com/superdesk/web-publisher/commit/XXX where XXX is the change hash

* 0.6.0
 * feature [#324] Added an option to sort collections
 * feature [#316] Add possibility to set custom headers and clear cookies with ResponseContext class.
 * feature [#314] Add more options to articles filtering api
 * feature [#312] Improve user registration 
 * feature [#312] Add Settings Bundle
 * feature [#309] Validate objects based on configured model's validation when content is pushed
 * feature [#295] Implemented article preview for users with special privileges
 * feature [#297] Add API to delete article
 * feature [#294] Add support for Manual Content List in API

* 0.5.0
 * feature [#281] Allow to filter articles by route id in API
 * feature [#280] Add correct Content-Type header to routes with extensions
 * feature [#279] Allow to filter articles by status in API
 * bug [#270] Rely on "evelovedfrom" property instead of a slug
 * bug [#268] Fixed filtering content lists items by criteria
 * feature [#212] Implement revisions system for containers
 * feature [#265] Add command to list tenants

* 0.2.0
 * feature [#235] Add API endpoint for rendering single container (and its content)
 * feature [#219] Automatically create menu widget when root menu/navigation is created
 * feature [#218] Assign article to route of type content automatically when article is published
 * feature [#215] Filter articles by metadata in gimmelist
 * feature [#213] Implement and expose article's keywords
 * feature [#211] Create Automatic list widget
 * feature [#209] Add option to ignore context meta in loaders 
 * feature [#209] Add rendition loader (gimme rendition) from article media
 * feature [#203] Added option to access theme assets with simple /public/{fileName} links
 * change [#207] Changed serialized properties naming strategy from underscore to camelCase

* 0.1.0-11
 * feature [#184] Added Google AMP HTML integration

* 0.1.0-10
 * feature [#182] [CoreBundle] Implement API token authentication
 * feature [#181] [MenuBundle] Implement possibility to move menu items
 * feature [#176] Added a new way to dynamically handle "resolve target entities"
 * feature [#176] Introduced better inheritance mapping

* 0.1.0-9
 * feature [#168] Added automatic content lists
 * feature [#173] Add user registration and login features
 
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