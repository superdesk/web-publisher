
# CHANGELOG

This changelog references the relevant changes (bug and security fixes).

To get the diff for a specific change, go to https://github.com/superdesk/web-publisher/commit/XXX where XXX is the change hash

## 2.3

* migration to PHP 8.0
* native Google Cloud Storage support
* slug change in Superdesk now affects article slug in the Publisher by default (with automatic redirection already available)
* changing category covered by publishing rule now updates article URL
* category change resulting article URL change is now addressed with proper redirection
* introducing `SLUG_REGEX` environment variable for handling slug duplication criteria
* fix for custom redirections
* fix for sorting articles by creating time in the Output control
* fix for the Error log view in the Publisher admin interface
* .env.(local.)example updates with AWS S3 and Google Cloud Storage configuration templates

## 2.2

* migration to Symfony 5
* various migration fixes
* fix for Publisher not being available for new users
* fix for article corrections not being published

## 2.1
* fix [#1183] for timestampable update error
* fix [#1186] get client original extension from file as a fallback
* fix [#1184] ES- Added asciifolding analyzer to package fields
* fix [#1181] force retry when the lock is aquired
* improvement [#1180] Use ES index aliases
* feature [#1177] Added support for photo license 
* fix [#1179] Fix _swp_analytics and sorting by page views
* fix [#1175] added article updated at timestamp to geo ip cache key
* improvement [#1150] Improve full text search ES queries
* fix [#1168] Attach article to content route on article publish
* fix [#1170] set x-forwarded-for from x-real-ip header
* feature [#1164] update to composer 2.0
* feature [#1169] added byline to the analytics export
* feature [#1167] get the author with the most articles
* feature [#1163] Add term suggestion to search results
* fix [#1155] catch fb ia exception and log it
* fix [#1151] fixed aws url generator
* fix fix docker installation guide url in manual
* fix [#1149] fix route name to prevent duplication conflict after fixtures on theme load
* fix [#1147] publish articles to fbia when clearly stated
* fix [#1146] fixed awss3 adapter
* fix [#1114] move api docs to openapi3 format and remove nelmio api bundle annotations from controllers
* feature [#1145] Split asset url resolvers
* fix [#1144] added migration - route description field
* feature [#1142] FUN-34 - add copyright notice
* feature [#1141] FUN-33 - add description to route
* improvement [#1140] improve article metadata matching against list criteria
* feature [#1139] configure messenger routing keys
* fix [#1137] check if metadata exist before removing
* fix [#1136] fixed filtering by multiple services and subjects
* fix [#1135] delete the format from the scheme
* fix [#1133] allow to paginate the list of menus in API
* fix [#1134] use throwable instead of exception, added retryOnConflict in ES config
* fix [#1132] change item headline to text
* fix [#1131] apple news layout improvements
* feature [#1130] exposed place in packages listing
* fix [#1129] allow to reset apple news config
* feature [#1123] Rewrite metadata
* feature [#1119] Redirect to dynamic route after successful login
* fix expose twitterMedia and ogMedia values in the seoMetadata object in twig
* feature [#1122] exposed profile field in article metadata
* fix [#1116] throw exception when theme installation fails
* improvement [#1112] improve errors messages for tenant not found and issues on connection with superdesk
* improvement [#1110] improve tenant create command
* fix [#1113] Fix oauth endpoints for tests
* fix [#1113] Replace implicit, hard coded, oauth endpoints with explicit endpoints from env
* fix [#1108] Apple news quote, byline and url fixes
* fix [#1107] apple news fixes
* fix [#1105] set text/html as default response content type for template rendering result
* fix [#1103] add docs for new original_url twig function
* fix [#1102] fix content list items ordering
* fix [#1102] add latest articles to content list when filter is empty on list update
* fix [#1099] handle migrated article orginal url and redirect it to new article location
* feature [#1093] pin articles on any position in content lists
* improvement [#1096] bump dependencies to allow usage on php 7.4
* fix [#1092] don't remove content list items when list is limited to 0
* improvement [#1091] allow symfony 5 in settings and storage bundles
* feature [#1086] Redirect old articles slugs to an existing article
* fix [#1084] delete authors API endpoint
* fix [#1085] set the host of the analytics report url
* fix [#1083)] Fixed setting apple news config
* feature [#1081] Apple News integration
* feature [#1082] add limit parameter and better logging to broken embedded images remover
* feature [#1080] add command to remove broken elements from articles body
* improvement [#1079] use sync queues in dev env
* fix [#1065] Fixed settings cache key, make default_language empty string
* fix [#1064] set validator for headline min length
* fix [#1063] fixed date times when null and export only published articles

## 2.1.0-RC

* improvement [#1062] Add "default language" to tenant configuration
* improvement [#0161] Allow filtering packages by language
* fix [#1060] Handle slideshow items position
* improvement [#1059] Improve media processing on article update
* feature [#1051] Failure Queue API
* improvement [#1050] Improve articles excluding by authors
* feature [#1041] Rewrite consumers implementation to one based on Symfony Messenger
* fix [#1049] Support Token Authenticator only in the API
* feature [#1025] Add analytics export 
* feature [#1017] Add redirect routes
* feature [#1009] Restrict access to articles based on geo-location
* improvement [#1008] Add env variable to customize S3 endpoint
* improvement [#1003] @mikeavena Improve install docs
* improvement [#1006] Filter articles by keywords
* improvement [#1001] Add public url to rendition
* improvement [#985]  @kottkrig  Add lazy load attribute to embedded images.
* improvement [#982] Add feature media to package endpoint response

## 2.0.4

 * fix [#1039] handle items with slugline property set to empty string
 * improvement [#1038] search articles by exact author name
 * feature [#0134] remove items exceeding content list limit automatically
 * feature [#1033] exclude articles by routes
 * fix [#1030] fix content push performance with many gallery items
 * fix [#1026] fix asset location resolver logic
 * improvement [#1021] update content list when new item is added to it
 * fix [#1020] handle case when multiple instances work with this same storage
 * fix [#1016] fix cache key generation (in twig)
 * improvement [#1015] make article media timestampable
 * fix [#1013] safely save loader parameters (in dev mode)
 * improvement [#1010] improve metadata matching in content lists filters
 * fix [#1005] fix article publishing with multiple tenant rules]

## 2.0.3

 * fix [#997] fix duplicated authors in package handling
 * feature [#996] handle new superdesk ways for unpublishing content
 * improvement [#994] change author biography from string to text in database
 * improvement [#993] expose article place for templators
 * improvement [#989] add images length to the Image model
 * improvement [#984] expose route parent for templators

## 2.0.2

 * improvement [#981] clean up old slideshows on article update
 * fix [#980] Fix tags generation for cache invalidation requests
 * improvement [#979] set generated classes target dir to var/cache in PackageHydrator

## 2.0.1

 * improvement [#970, #973] Use varnish xtags instead cachetags
 * fix [#969] Properly save slideshows when package is updated
 * fix [#964] Fix slow content list items loading (internal api)
 * fix [#963] Display slideshow's items in a proper order by default
 * fix [#958] Log this same (and more) data to filesystem and graylog

## 2.0.0
 * feature [#942] Redirect not found article request to route page  - controlled by ENV variable, disabled by default
 * feature [#925] oAuth client implementation
 * BC BREAK [#834] Changed query params publishedBefore and publishedAfter to published_before and published_after in ES bundle
 * feature [#787] Added support for SEO metadata
 * BC BREAK/cleanup [#778] Remove Container, Widgets and Revisions concepts from Templates System
 * BC BREAK/feature [#643] Added support for Symfony 4.2 & Flex & PHP 7.3
 * improvement [#630] Add option to exclude articles from content list items
 * bug [#784] Fixed search pagination
 * bug [#763] Expose more metadata in article media
 * bug [#754] Always cast publishedBefore and publishedAfter parameters to object of type DateTime
 * feature [#758] Get next and previous article from content list (in template)
 * feature [#740] Related articles
 * feature [#732] Set image byline/credit metadata field in the HTML output
 * improvement [#730] Allow to filter articles by data stored in extra

## 1.5.0
 * improvement [#727] Turn off loggable extensions on tables
 * feature [#724] Schedule article adding to manual content lists
 * fix [#722] Fix data send via webhook
 * bug [#720] Convert editor3 embedded images format to editor2 format
 * feature [#715] Allow to change the article's slug based on settings
 * fix [#714] Fix pageviews counting in case of async requests processing
 * feature [#713] Add option do define rendition used for article body images
 * BC BREAK feature [#711] Move content push processing to queue - require consumer process running 
 * bug [#708] Allow to render AMP HTML version of content with route of type "content"
 * feature [#706] Added Console Command which processes articles' body
 * feature [#705] Download media assets whan not found in Publisher database
 * feature [#699] Allow to blacklist specific article keywords
 * feature [#696] Allow to use first published date as article publish date
 * bug [#693] Do not add articles to automatic content lists without filters set
 * fix [#689] By default add new item to top of automatic content ligst
 * feature [#674] Added webhook for generating preview URL
 * feature [#670] Enable password reset
 * feature [#669] Added command to import users from JSON files
 * feature [#666] Expose values for social accounts for authors
 * feature [#665] Added route to redirect to an article page by article slug
 * feature [#649] Render media in preview when article is not yet published
 * feature [#658] Option to store article comments count
 * improvement [#648] Exclude non-publisher created routes from RouteProvider
 * bug [#647] Do not allow empty params in ContentListsItemLoader
 * bug [#646] Do not enable tenantable filter in articles count handler
 * feature [#645] Loader for keyword
 * improvement [#642] Change fbia to isPublishedFbia
 * bug [#641] Fix the assets URLs generating by Media Manager
 * bug [#640] Paywall securing articles doesn't work
 * bug [#639] Error in generating url to avatar image
 * improvement [#630] Add otpion to exclude articles fron content list items

## 1.4.0
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

## 1.3.0
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
 
## 1.2.0
 * feature [#394] Add "template_name" parameter to html widget
 * feature [#393] Introduce "without" keyword for gimmelist and add refactor article sources to allow better filtering
 * [BC Break] feature [#371] Handle article sources (Article source is now an array of sources instead of a string.)
 * feature [#379] Add API endpoint for listing available widgets templates in current theme
 * feature [#377] Add API endpoint for package update (pubStatus)
 * [BC Break] fix [#376] Make domainName field required in tenant create API
 * feature [#372] Add Liveblog widget, add external ESI renderer
 * feature [#368] Add Content List Loader

## 1.1.0
 * feature [#364] Add two new user settings
 * improvement [#360] Allow multiple routes filtering in articles loader
 * improvement [#359] Allow removing about field content in user profile
 * fix [#353] Fix content list issue when publishing item
 * improvement [#352] Add caching to static theme assets
 * fix [#350] Add genre property to NINJS schema validator 

## 1.0.1
 * feature [RuleBundle] Implemented name and description fields in Rules API 

## 1.0.0
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

## 0.6.0
 * feature [#324] Added an option to sort collections
 * feature [#316] Add possibility to set custom headers and clear cookies with ResponseContext class.
 * feature [#314] Add more options to articles filtering api
 * feature [#312] Improve user registration 
 * feature [#312] Add Settings Bundle
 * feature [#309] Validate objects based on configured model's validation when content is pushed
 * feature [#295] Implemented article preview for users with special privileges
 * feature [#297] Add API to delete article
 * feature [#294] Add support for Manual Content List in API

## 0.5.0
 * feature [#281] Allow to filter articles by route id in API
 * feature [#280] Add correct Content-Type header to routes with extensions
 * feature [#279] Allow to filter articles by status in API
 * bug [#270] Rely on "evelovedfrom" property instead of a slug
 * bug [#268] Fixed filtering content lists items by criteria
 * feature [#212] Implement revisions system for containers
 * feature [#265] Add command to list tenants

## 0.2.0
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

## 0.1.0-11
 * feature [#184] Added Google AMP HTML integration

## 0.1.0-10
 * feature [#182] [CoreBundle] Implement API token authentication
 * feature [#181] [MenuBundle] Implement possibility to move menu items
 * feature [#176] Added a new way to dynamically handle "resolve target entities"
 * feature [#176] Introduced better inheritance mapping

## 0.1.0-9
 * feature [#168] Added automatic content lists
 * feature [#173] Add user registration and login features
 
## 0.1.0-8
 * bug/feature/maintenance [#165] Switch to ORM as main storage backend 

## 0.1.0-7
 * maintenance [#156] added memcached to project requirements and configured it as default sessions handler
 * maintenance [#156] Specified project requirements
 * feature [#155] Upgrade Symfony version to 3.1
 * feature [#153] Bump lowest PHP version to ^7.0
 * bug [#152] [ContentBundle] If I correct a headline, it and its article are published in addition to the original (takeit)

## 0.1.0-6
 * feature [#138] [RuleBundle][Rule][ContentBundle] Added content to route mapping with simple rules managed by API (takeit)
 * feature [#139] Add default templates for error pages.
 * feature [#128] [ContentBundle] Add route type constraint validator (takeit)
 * bug [#128] [ContentBundle] Improve possibility to un-assign/assign parent routes (takeit)
 * feature [#132] [Template System][TemplateEngine Bundle] implement pagination in gimmelist

## 0.1.0-5 
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
