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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const URL = 'https://download.maxmind.com/app/geoip_download?edition_id=%s&license_key=%s&suffix=tar.gz';

    private const PATH = '%s/%s.mmdb';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('swp_geo_ip');
        $rootNode = $treeBuilder->getRootNode();
        $this->normalizeDatabaseUrl($rootNode);
        $this->normalizeDatabasePath($rootNode);
        $rootNode
            ->beforeNormalization()
            ->ifTrue(static function ($v): bool {
                return
                    is_array($v) &&
                    array_key_exists('license_key', $v) &&
                    array_key_exists('edition_id', $v);
            })
            ->then(static function (array $v): array {
                $v['database_url'] = sprintf(self::URL, urlencode($v['edition_id']), urlencode($v['license_key']));

                return $v;
            });

        $rootNode
            ->children()
                ->scalarNode('license_key')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->info('MaxMind license key')
                ->end()
                ->scalarNode('database_url')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('edition_id')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->info('MaxMind database id')
                ->end()
                ->scalarNode('database_path')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->info('Path to the downloaded GeoIP2 database')
                ->end()
            ->end();

        return $treeBuilder;
    }

    private function normalizeDatabaseUrl(ArrayNodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifTrue(static function ($v): bool {
                return
                    is_array($v) &&
                    array_key_exists('license_key', $v) &&
                    array_key_exists('edition_id', $v);
            })
            ->then(static function (array $v): array {
                $v['database_url'] = sprintf(self::URL, urlencode($v['edition_id']), urlencode($v['license_key']));

                return $v;
            });
    }

    private function normalizeDatabasePath(ArrayNodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifTrue(static function ($v): bool {
                return is_array($v) && array_key_exists('edition_id', $v);
            })
            ->then(static function (array $v): array {
                $v['database_path'] = sprintf(self::PATH, '%kernel.project_dir%/var', $v['edition_id']);

                return $v;
            });
    }
}
