
CHANGELOG for version <= 1.5.x
==============================

This changelog references the relevant changes (bug and security fixes) done in <= 1.1 minor versions.

To get the diff for a specific change, go to https://github.com/superdesk/web-publisher/commit/XXX where XXX is the change hash

### 1.5.0
 * improvement [#630] Add option to exclude articles fron content list items

### 1.4.1
 * improvement [#648] Exclude non-publisher created routes from RouteProvider
 * bug [#647] Do not allow empty params in ContentListsItemLoader
 * bug [#646] Do not enable tenantable filter in articles count handler
 * feature [#645] Loader for keyword
 * bug [#641] Fix the assets URLs generating by Media Manager
 * bug [#640] Paywall securing articles doesn't work
 * bug [#639] Error in generating url to avatar image

### 1.4.0
 * feature [#626] Allow to store files on AWS
 * feature [#621] Added support for audio/video files
 * feature [#620, #625] Add support for auth JWT tokens (used by Coral Talk)
 * feature [#618] Implement article CTR calculations
 * improvement [#615] Add option reorder routes
 * improvement [#613] Add collection loading to route loader
 * improvement [#612] Add exclude_article handling to article loader
 * feature [#608] Invalidate varnish cache for article and route page when article is updated
 * feature [#607] Add time ago extension to twig
 * improvement [#603] Add option to force remove tenant
 * feature [#593] Added support for media lists/slideshows
 * feature [#595] Return paywallSecured property in the "evaluate" API endpoint
 * improvement [#587] Update livesite editor
 * feature [#583] Allow to mark articles as "paywall-secured" using rules and destinations
 * bug [#581] Use domain name from tenant as a value of cookie_domain
 * feature [#577] Implement API for package external data
 * feature [#574] Paywall implementation
 * feature [#573] Collect article impressions data in database
 * improvement [#654] Update vendors to symfony4 compatible versions
 * improvement [#560] Add sufix (generated from package guid) for duplicated article slugs
 * bug [#556] Fixed boolean values in Settings API
 * feature [#548] Added periodic ping calls to keep WebSocket connection alive
 * bug [#544] List only published articles in content lists on frontend
 * feature [#539] Store article author images in publisher
 * bug [#531] Assign package to theme generated articles
 * bug [#530] Authors without slug after publishing article
 * bug [#525] Fixed the status of already published package after the correction
 * improvement [#524] Allow to set published to false when publishing an article
 * improvement [#521] Unpublish articles (status new) and package (status usable) when it's route is removed
 * improvement [#511] Add slug property to author meta
 * feature [#506] Wordpress output channel adapter
 * feature [#503] Added push integration/notification about newly created package based on WebSocket
 * bug [#499] Fixed package preview when template is not set or does not exist
 * improvement [#497] Upload theme always to generated (from theme name) directory
 * bug [#494] Fixed article's slug line so it can handle chinese chars
 * feature [#488] Secure content push to Publisher (option to set secret for organization)
 * bug [#479] Fix article author loaders (sorting) and article media loader (cache key)
 * improvement [#465] allow filtering by route name in content lists
 * feature [#459] Added an option to preview an article before even publishing it

### 1.3.0
 * feature [#462] Added template widget
 * bug [#460] Install assets when theme is installed
 * feature [#458] Added redirect and notFound functions for twig templates
 * feature [#454] Override existing rules by publish destinations
 * improvement [#455] Added slug to article author - allow to load authors by slug
 * feature [#455] Allow to create custom routes (variable prefix and requirements)
 * feature [#451] Allowed to evaluate rules that match given package
 * feature [#453] Implemented bulk updates - settings API
 * feature [#447] Generate route's slug based on route's name if not provided by default
 * feature [#445] Implemented a list of optional values to theme's settings
 * feature [#444] Added an to get a single route by name and slug 
 * feature [#441] Added an option to list articles' authors in Twig templates
 * feature [#439] Added support for theme settings and logo upload
 * feature [#436] Added support for ordering by articles page views in selected date range
 * bug [#431] ignore www prefix in theme resolver
 * feature [#429] Implemented articles loading by routes static prefix and allowing to load articles from route children's
 * feature [#434] Add support for custom fields
 * feature [#428] Added support for authors
 * bug [#422] Eliminated rules regression, where tenant rules were not executed and refactored the way rules are handled.
 * improvement [#427] Improved the way how the article slug is generated
 * bug [#426] Allow different date time format when filtering articles by date range
 * bug [#425] Fixed losing alt attribute in images body
 * feature [#424] Added option to define in theme config elements (route, articles and more) to be generated on theme installation
 * improvement [#423] Add slug field to route. It will be used for url generation instead name field
 * feature [#420] Add option to order list by pageViews parameter
 * feature [#420] Add statistics to articles. Collect page views and make them visible in template and api
 * feature [#416] Add Webhooks system (with API) to publisher
 
### 1.2.0
 * feature [#394] Add "template_name" parameter to html widget
 * feature [#393] Introduce "without" keyword for gimmelist and add refactor article sources to allow better filtering
 * [BC Break] feature [#371] Handle article sources (Article source is now an array of sources instead of a string.)
 * feature [#379] Add API endpoint for listing available widgets templates in current theme
 * feature [#377] Add API endpoint for package update (pubStatus)
 * [BC Break] fix [#376] Make domainName field required in tenant create API
 * feature [#372] Add Liveblog widget, add external ESI renderer
 * feature [#368] Add Content List Loader

### 1.1.0
 * feature [#364] Add two new user settings
 * improvement [#360] Allow multiple routes filtering in articles loader
 * improvement [#359] Allow removing about field content in user profile
 * fix [#353] Fix content list issue when publishing item
 * improvement [#352] Add caching to static theme assets
 * fix [#350] Add genre property to NINJS schema validator 

### 1.0.1
 * feature [RuleBundle] Implemented name and description fields in Rules API 

### 1.0.0
 * fix Adding article to content lists on publish (#349)
 * fix Set default value for urgency, handle genre as array in package item
 * feature ElasticSearch integration & bugfixes (#336)
 * feature Use organization instead tenant in user entity
 * feature [UserBundle] add API to promote and demote users (add/remove user roles)
 * feature Add articles count to tenant and article api endpoints
 * feature Add loader for content list items
 * feature [UserBundle][CoreBundle] add user profile update and get API, add option to set tenant from console command with optional argument
 * feature [Content Bundle] add option to filter articles by criteria
 
 Full list of commits: https://github.com/superdesk/web-publisher/compare/cd75f77...046da8f

### 0.6.0
 * feature [#324] Added an option to sort collections
 * feature [#316] Add possibility to set custom headers and clear cookies with ResponseContext class.
 * feature [#314] Add more options to articles filtering api
 * feature [#312] Improve user registration 
 * feature [#312] Add Settings Bundle
 * feature [#309] Validate objects based on configured model's validation when content is pushed
 * feature [#295] Implemented article preview for users with special privileges
 * feature [#297] Add API to delete article
 * feature [#294] Add support for Manual Content List in API

### 0.5.0
 * feature [#281] Allow to filter articles by route id in API
 * feature [#280] Add correct Content-Type header to routes with extensions
 * feature [#279] Allow to filter articles by status in API
 * bug [#270] Rely on "evelovedfrom" property instead of a slug
 * bug [#268] Fixed filtering content lists items by criteria
 * feature [#212] Implement revisions system for containers
 * feature [#265] Add command to list tenants

### 0.2.0
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

### 0.1.0-11
 * feature [#184] Added Google AMP HTML integration

### 0.1.0-10
 * feature [#182] [CoreBundle] Implement API token authentication
 * feature [#181] [MenuBundle] Implement possibility to move menu items
 * feature [#176] Added a new way to dynamically handle "resolve target entities"
 * feature [#176] Introduced better inheritance mapping

### 0.1.0-9
 * feature [#168] Added automatic content lists
 * feature [#173] Add user registration and login features
 
### 0.1.0-8
 * bug/feature/maintenance [#165] Switch to ORM as main storage backend 

### 0.1.0-7
 * maintenance [#156] added memcached to project requirements and configured it as default sessions handler
 * maintenance [#156] Specified project requirements
 * feature [#155] Upgrade Symfony version to 3.1
 * feature [#153] Bump lowest PHP version to ^7.0
 * bug [#152] [ContentBundle] If I correct a headline, it and its article are published in addition to the original (takeit)

### 0.1.0-6
 * feature [#138] [RuleBundle][Rule][ContentBundle] Added content to route mapping with simple rules managed by API (takeit)
 * feature [#139] Add default templates for error pages.
 * feature [#128] [ContentBundle] Add route type constraint validator (takeit)
 * bug [#128] [ContentBundle] Improve possibility to un-assign/assign parent routes (takeit)
 * feature [#132] [Template System][TemplateEngine Bundle] implement pagination in gimmelist

### 0.1.0-5 
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
