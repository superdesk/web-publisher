<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Geo IP Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\GeoIPBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('swp_geo_ip');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('database_url')
                    ->cannotBeEmpty()
                    ->defaultValue('https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz')
                    ->info('GeoIP2 database URL')
                ->end()
                ->scalarNode('database_path')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.cache_dir%/GeoLite2-City.mmdb')
                    ->info('Path to the downloaded GeoIP2 database')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
