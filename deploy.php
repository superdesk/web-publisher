<?php

/*
 * This file is part of the Superdesk Web Publisher project.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace Deployer;

require 'recipe/symfony.php';

// Configuration
set('ssh_type', 'native');
set('ssh_multiplexing', true);

set('env_vars', 'SYMFONY_ENV={{env}} DATABASE_USER={{database_user}} DATABASE_NAME={{database_name}} DATABASE_PORT={{database_port}} DATABASE_PASSWORD={{database_password}}');

set('writable_dirs', ['web/uploads']);
set('repository', 'https://github.com/superdesk/web-publisher.git');
set('default_stage', 'local');
set('composer_options', function () {
    $options = '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader';

    return get('env') !== 'prod' ? $options : sprintf('%s --no-dev', $options);
});

set('clear_paths', function () {
    return get('env') !== 'prod' ? ['web/config.php'] : ['web/app_*.php', 'web/config.php'];
});

// Servers
serverList('app/config/servers.yml.dist');

// Tasks
desc('Clear Doctrine cache');
task('doctrine:cache', function () {
    run('{{env_vars}} {{bin/php}} {{bin/console}} doctrine:cache:clear main_cache');
});
after('deploy:cache:warmup', 'doctrine:cache');

desc('Install themes assets');
task('theme:assets:install', function () {
    run('{{env_vars}} {{bin/php}} {{bin/console}} sylius:theme:assets:install');
});
after('deploy:assets:install', 'theme:assets:install');

desc('Rollback database');
task('rollback:db', function () {
    run('{{env_vars}} {{bin/php}} {{bin/console}} doctrine:migrations:migrate prev');
});
after('rollback', 'rollback:db');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
