# Upgrade notes: 2.5 -> 3.0 (Symfony 7.4 / PHP 8.5)

## Platform

* PHP **8.3+** required, runtime target is **PHP 8.5** (docker image `php:8.5-fpm-alpine`).
* Symfony **7.4 LTS** (upgraded from 5.4 through 6.4).
* Doctrine ORM 2.20 / DBAL 3, PHPCR-ODM 2, Symfony CMF Routing 3, Twig 3, Flysystem 3,
  Guzzle 7, JMS Serializer Bundle 5, KNP Paginator 6.

## Removed features

* **Google AMP HTML support** was removed entirely (packages `takeit/amp`,
  `superdeskwebpublisher/amp-html-bundle`, the per-tenant `ampEnabled` flag, AMP theme
  loading and the `amp/amp-theme` theme directories). Migration `Version20260612000000`
  drops the `swp_tenant.amp_enabled` column.
* **Facebook Instant Articles** support was removed entirely (FBIA bundle, controllers,
  models, the `fbia_enabled` tenant setting, `isPublishedFbia` on publish destinations
  and articles, the FBIA bucket content-list flow). Migration `Version20260612000001`
  drops the `swp_fbia_*` tables and related columns.

## WebSockets replaced by Mercure

`gos/web-socket-bundle` + `cboden/ratchet` (and the AMQP pusher + the dedicated
websocket server process) were replaced by **Mercure** (`symfony/mercure-bundle`).
Package create/update notifications are published to the `swp/package` topic on the
Mercure hub. Consumers should subscribe via SSE on `MERCURE_PUBLIC_URL`.
New env vars: `MERCURE_URL`, `MERCURE_PUBLIC_URL`, `MERCURE_JWT_SECRET`
(the `WEBSOCKET_*` and websocket supervisor entries are gone); docker-compose now
ships a `dunglas/mercure` service.

## Internal replacements

* `burgov/key-value-form-bundle` -> `SWP\Bundle\StorageBundle\Form\Type\KeyValueType`.
* `twig/cache-extension` + `emanueleminotto/twig-cache-bundle` -> vendored Twig 3 port
  under `SWP\Component\TwigCacheExtension` (same `{% cache %}` tag and strategies).
* `twig/extensions` Text/Intl filters -> `SWP\Bundle\CoreBundle\Twig\LegacyFiltersExtension`
  (`truncate`, `wordwrap`, `localizeddate`, `localizednumber`, `localizedcurrency`).
* `takeit/stringy` -> `voku/stringy` (same `Stringy\*` API for theme filters).
* `ocramius/generated-hydrator` -> reflection-based property copy in `PackageHydrator`.
* `litipk/flysystem-fallback-adapter` -> `SWP\Bundle\ContentBundle\Flysystem\FallbackAdapter`.
* `apache/log4php` and `symfony/swiftmailer-bundle` removed (Monolog / symfony/mailer).

## API behaviour notes

* The CMF request attribute holding the resolved template is now `template`
  (was `contentTemplate`).
* Route `static_prefix` / `variable_pattern` still serialize as `null` when unset;
  unset static prefixes are stored as SQL `NULL` via the `empty_string_or_null_string`
  DBAL type to keep the `(staticprefix, tenant_code)` unique index semantics.

## Known remaining work

* Behat: `behatch/contexts` is not Symfony-7 compatible and was removed; the required
  step definitions need to be vendored into `SWP\Behat` before the behat suite runs.
* Doctrine ORM 3: the YAML mapping driver is removed in ORM 3; the `*.orm.yml` mappings
  must be converted (XML recommended, preserving the per-bundle override layout)
  before bumping `doctrine/orm` to ^3.
* A number of functional tests fail; note that the develop branch CI was already red
  before this upgrade, so not all failures are regressions.
