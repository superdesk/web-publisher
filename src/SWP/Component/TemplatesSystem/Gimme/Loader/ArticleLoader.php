<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Loader;

use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Yaml\Parser;

class ArticleLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @param string      $rootDir     path to application root directory
     * @param MetaFactory $metaFactory
     */
    public function __construct($rootDir, MetaFactory $metaFactory)
    {
        $this->rootDir = $rootDir;
        $this->metaFactory = $metaFactory;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Meta Loader provide simple way to test Loader, it will be removed when real loaders will be merged.",
     *     parameters={}
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return Meta|array false if meta cannot be loaded, a Meta instance otherwise
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::SINGLE)
    {
        if (!is_readable($this->rootDir.'/Resources/meta/article.yml')) {
            throw new \InvalidArgumentException('Configuration file is not readable for parser');
        }
        $parser = new Parser();
        $configuration = (array) $parser->parse(file_get_contents($this->rootDir.'/Resources/meta/article.yml'));

        if ($responseType === LoaderInterface::SINGLE) {
            return $this->metaFactory->create([
                'title' => 'New article',
                'keywords' => 'lorem, ipsum, dolor, sit, amet',
                'don\'t expose it' => 'this should be not exposed',
            ], $configuration);
        } elseif ($responseType === LoaderInterface::COLLECTION) {
            return [
                $this->metaFactory->create([
                    'title' => 'New article 1',
                    'keywords' => 'lorem, ipsum, dolor, sit, amet',
                    'don\'t expose it' => 'this should be not exposed',
                ], $configuration),
                $this->metaFactory->create([
                    'title' => 'New article 2',
                    'keywords' => 'lorem, ipsum, dolor, sit, amet',
                    'don\'t expose it' => 'this should be not exposed',
                ], $configuration),
            ];
        }
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported($type)
    {
        return in_array($type, ['articles', 'article']);
    }
}
