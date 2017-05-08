Superdesk Web Publisher
=======================

[![Build Status](https://travis-ci.org/superdesk/web-publisher.svg?branch=master)](https://travis-ci.org/superdesk/web-publisher)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/superdesk/web-publisher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/superdesk/web-publisher/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56bc97382a29ed00396b3760/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56bc97382a29ed00396b3760)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c1d40e6d-f4c3-42fa-af0e-d4a4e521d435/mini.png)](https://insight.sensiolabs.com/projects/c1d40e6d-f4c3-42fa-af0e-d4a4e521d435)

Superdesk Publisher - the next generation publishing platform for journalists and newsrooms.

*The Superdesk Publisher is an API-centric delivery tool for all digital platforms. Written from scratch in 2016, it utilizes the knowledge gained from 17 years of delivering digital news at scale with [Newscoop][2]. The Publisher is designed to work with any editorial system. Naturally, it works the best with our in-house newsroom management system, [Superdesk][3]. Therefore, it allows independent maintenance, upgrade and change of the editorial back-end system.*

## Documentation

Full documentation can be found here: [http://superdesk-publisher.readthedocs.org][1]

## Requirements

 * PHP >= 7.0
   * iconv needs to be enabled
   * Intl needs to be installed with ICU 4+
   * pdo needs to be enabled
   * JSON needs to be enabled
   * ctype needs to be enabled
   * Your php.ini needs to have the date.timezone setting
   * PHP tokenizer needs to be enabled
   * mbstring functions need to be enabled
   * POSIX needs to be enabled (only on *nix)
   * CURL and php-curl need to be enabled
   * php.ini recommended settings
     * short_open_tag = Off
     * magic_quotes_gpc = Off
     * register_globals = Off
     * session.auto_start = Off
 * Postgresql >= 9.4
   * pdo-pgsql
 * Memcached
   * memcached (running)
   * php-memcached

## Installation

See [installation guide](install.md) for more details.

[1]: http://superdesk-publisher.readthedocs.org/en/latest/
[2]: https://www.sourcefabric.org/en/newscoop/
[3]: https://www.superdesk.org/

## Testing

See [detailed instructions](testing.md) for more details.

## License

See the complete license [here](LICENSE.md).

## Contributors

This project is a Sourcefabric z.ú. and contributors initiative.

List of all authors and contributors can be found [here](AUTHORS.md).

## Superdesk Publisher is possible thanks to other Sourcefabric initiatives:

Symfony Bundles:

| Name | CI Status |
| --- | --- |
| [swp/multi-tenancy-bundle](https://github.com/SuperdeskWebPublisher/SWPMultiTenancyBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPMultiTenancyBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPMultiTenancyBundle) |
| [swp/rule-bundle](https://github.com/SuperdeskWebPublisher/SWPRuleBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPRuleBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPRuleBundle) |
| [swp/storage-bundle](https://github.com/SuperdeskWebPublisher/SWPStorageBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPStorageBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPStorageBundle) |
| [swp/bridge-bundle](https://github.com/SuperdeskWebPublisher/SWPBridgeBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPBridgeBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPBridgeBundle) |
| [swp/templates-system-bundle](https://github.com/SuperdeskWebPublisher/SWPTemplatesBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPTemplatesSystemBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPTemplatesSystemBundle) |
| [swp/settings-bundle](https://github.com/SuperdeskWebPublisher/SWPSettingsBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPSettingsBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPSettingsBundle) |
| [swp/content-list-bundle](https://github.com/SuperdeskWebPublisher/SWPContentListBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPContentListBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPContentListBundle) |
| [swp/menu-bundle](https://github.com/SuperdeskWebPublisher/SWPMenuBundle) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPMenuBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPMenuBundle) |

Components:

| Name | CI Status |
| --- | --- |
| [swp/templates-system](https://github.com/SuperdeskWebPublisher/templates-system) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/templates-system.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/templates-system) |
| [swp/multi-tenancy](https://github.com/SuperdeskWebPublisher/multi-tenancy) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/multi-tenancy.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/multi-tenancy) |
| [swp/rule](https://github.com/SuperdeskWebPublisher/rule) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/rule.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/rule) |
| [swp/storage](https://github.com/SuperdeskWebPublisher/storage) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/storage.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/storage) |
| [swp/bridge](https://github.com/SuperdeskWebPublisher/bridge) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/bridge.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/bridge)  |
| [swp/common](https://github.com/SuperdeskWebPublisher/common) | [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/common.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/common)  |
