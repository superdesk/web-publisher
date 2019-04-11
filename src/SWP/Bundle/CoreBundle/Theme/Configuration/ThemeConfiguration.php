<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 *
 *
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SWP\Bundle\CoreBundle\Theme\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 */
final class ThemeConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sylius_theme');
        $rootNodeDefinition = $treeBuilder->getRootNode();

        $rootNodeDefinition->ignoreExtraKeys();

        $this->addRequiredNameField($rootNodeDefinition);
        $this->addOptionalTitleField($rootNodeDefinition);
        $this->addOptionalDescriptionField($rootNodeDefinition);
        $this->addOptionalPathField($rootNodeDefinition);
        $this->addOptionalParentsList($rootNodeDefinition);
        $this->addOptionalScreenshotsList($rootNodeDefinition);
        $this->addOptionalAuthorsList($rootNodeDefinition);
        $this->addOptionalConfig($rootNodeDefinition);
        $this->addOptionalGeneratedData($rootNodeDefinition);
        $this->addOptionalSettingsList($rootNodeDefinition);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addRequiredNameField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('name')->isRequired()->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalTitleField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('title')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalDescriptionField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('description')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalPathField(ArrayNodeDefinition $rootNodeDefinition)
    {
        $rootNodeDefinition->children()->scalarNode('path')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalParentsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $parentsNodeDefinition = $rootNodeDefinition->children()->arrayNode('parents');
        $parentsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
            ->prototype('scalar')
            ->cannotBeEmpty()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalConfig(ArrayNodeDefinition $rootNodeDefinition)
    {
        $configNodeDefinition = $rootNodeDefinition->children()->arrayNode('config');
        $configNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
            ->prototype('variable')
        ;

        $configNodeDefinition->children()->variableNode('defaultTemplates');
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalGeneratedData(ArrayNodeDefinition $rootNodeDefinition)
    {
        $generatedDataNodeDefinition = $rootNodeDefinition->children()->arrayNode('generatedData');
        $this->addOptionalGeneratedRoutesData($generatedDataNodeDefinition);
        $this->addOptionalGeneratedMenusData($generatedDataNodeDefinition);
        $this->addOptionalGenerateContentListsData($generatedDataNodeDefinition);
    }

    /**
     * @param ArrayNodeDefinition $generatedDataNodeDefinition
     */
    private function addOptionalGeneratedRoutesData(ArrayNodeDefinition $generatedDataNodeDefinition)
    {
        $routesNodeDefinition = $generatedDataNodeDefinition->children()->arrayNode('routes');
        $routesNodeDefinition->requiresAtLeastOneElement();

        /** @var ArrayNodeDefinition $routeNodeDefinition */
        $routeNodeDefinition = $routesNodeDefinition->prototype('array');
        $routeNodeDefinition
            ->validate()
            ->ifTrue(function ($route) {
                return [] === $route;
            })
            ->thenInvalid('Route cannot be empty!')
        ;

        $routeNodeBuilder = $routeNodeDefinition->children();
        $routeNodeBuilder->scalarNode('name')->cannotBeEmpty();
        $routeNodeBuilder->scalarNode('slug')->cannotBeEmpty();
        $routeNodeBuilder->scalarNode('type')->cannotBeEmpty();
        $routeNodeBuilder->scalarNode('parent')->defaultNull();
        $routeNodeBuilder->scalarNode('templateName')->defaultNull();
        $routeNodeBuilder->scalarNode('articlesTemplateName')->defaultNull();
        $routeNodeBuilder->scalarNode('numberOfArticles')->defaultNull();
    }

    /**
     * @param ArrayNodeDefinition $generatedDataNodeDefinition
     */
    private function addOptionalGeneratedMenusData(ArrayNodeDefinition $generatedDataNodeDefinition)
    {
        $menusNodeDefinition = $generatedDataNodeDefinition->children()->arrayNode('menus');
        $menusNodeDefinition->requiresAtLeastOneElement();

        /** @var ArrayNodeDefinition $menuNodeDefinition */
        $menuNodeDefinition = $menusNodeDefinition->prototype('array');
        $menuNodeDefinition
            ->validate()
            ->ifTrue(function ($menu) {
                return [] === $menu;
            })
            ->thenInvalid('Menu cannot be empty!')
        ;

        $menuNodeBuilder = $menuNodeDefinition->children();
        $menuNodeBuilder->scalarNode('name')->cannotBeEmpty()->end();
        $menuNodeBuilder->scalarNode('label')->cannotBeEmpty()->end();
        $menuNodeBuilder->scalarNode('uri')->cannotBeEmpty()->end();
        $menuNodeBuilder->scalarNode('route')->cannotBeEmpty()->defaultNull()->end();

        // 1st level of children
        $childrensNodeDefinition = $menuNodeBuilder->arrayNode('children');
        /** @var ArrayNodeDefinition $childrenNodeDefinition */
        $childrenNodeDefinition = $childrensNodeDefinition->prototype('array');
        $childrenNodeDefinition
            ->validate()
            ->ifTrue(function ($menu) {
                return [] === $menu;
            })
            ->thenInvalid('Menu cannot be empty!')
        ;

        $childrenNodeBuilder = $childrenNodeDefinition->children();
        $childrenNodeBuilder->scalarNode('name')->cannotBeEmpty()->end();
        $childrenNodeBuilder->scalarNode('label')->cannotBeEmpty()->end();
        $childrenNodeBuilder->scalarNode('uri')->cannotBeEmpty()->end();
        $childrenNodeBuilder->scalarNode('route')->cannotBeEmpty()->defaultNull()->end();

        // 2nd level of children
        $childrensChildrensNodeDefinition = $childrenNodeBuilder->arrayNode('children');
        /** @var ArrayNodeDefinition $childrensChildrenNodeDefinition */
        $childrensChildrenNodeDefinition = $childrensChildrensNodeDefinition->prototype('array');
        $childrensChildrenNodeDefinition
            ->validate()
            ->ifTrue(function ($menu) {
                return [] === $menu;
            })
            ->thenInvalid('Menu cannot be empty!')
        ;

        $childrenNodeBuilder = $childrensChildrenNodeDefinition->children();
        $childrenNodeBuilder->scalarNode('name')->cannotBeEmpty()->end();
        $childrenNodeBuilder->scalarNode('label')->cannotBeEmpty()->end();
        $childrenNodeBuilder->scalarNode('uri')->cannotBeEmpty()->end();
        $childrenNodeBuilder->scalarNode('route')->cannotBeEmpty()->defaultNull()->end();
    }

    private function addOptionalGenerateContentListsData(ArrayNodeDefinition $generatedDataNodeDefinition)
    {
        $contentListsNodeDefinition = $generatedDataNodeDefinition->children()->arrayNode('contentLists');
        $contentListsNodeDefinition->requiresAtLeastOneElement();

        /** @var ArrayNodeDefinition $contentListNodeDefinition */
        $contentListNodeDefinition = $contentListsNodeDefinition->prototype('array');
        $contentListNodeDefinition
            ->validate()
            ->ifTrue(function ($list) {
                return [] === $list;
            })
            ->thenInvalid('Content List cannot be empty!')
        ;

        $listNodeBuilder = $contentListNodeDefinition->children();
        $listNodeBuilder->scalarNode('name')->cannotBeEmpty()->end();
        $listNodeBuilder->scalarNode('type')->cannotBeEmpty()->end();
        $listNodeBuilder->scalarNode('description')->defaultNull()->end();
        $listNodeBuilder->scalarNode('limit')->defaultNull()->end();
        $listNodeBuilder->scalarNode('cacheLifeTime')->defaultNull()->end();
        $listNodeBuilder->scalarNode('filters')->defaultNull()->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalScreenshotsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $screenshotsNodeDefinition = $rootNodeDefinition->children()->arrayNode('screenshots');
        $screenshotsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $screenshotNodeDefinition */
        $screenshotNodeDefinition = $screenshotsNodeDefinition->prototype('array');

        $screenshotNodeDefinition
            ->validate()
            ->ifTrue(function ($screenshot) {
                return [] === $screenshot || ['path' => ''] === $screenshot;
            })
            ->thenInvalid('Screenshot cannot be empty!')
        ;
        $screenshotNodeDefinition
            ->beforeNormalization()
            ->ifString()
            ->then(function ($value) {
                return ['path' => $value];
            })
        ;

        $screenshotNodeBuilder = $screenshotNodeDefinition->children();
        $screenshotNodeBuilder->scalarNode('path')->isRequired();
        $screenshotNodeBuilder->scalarNode('title')->cannotBeEmpty();
        $screenshotNodeBuilder->scalarNode('description')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalAuthorsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $authorsNodeDefinition = $rootNodeDefinition->children()->arrayNode('authors');
        $authorsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $authorNodeDefinition */
        $authorNodeDefinition = $authorsNodeDefinition->prototype('array');
        $authorNodeDefinition
            ->validate()
            ->ifTrue(function ($author) {
                return [] === $author;
            })
            ->thenInvalid('Author cannot be empty!')
        ;

        $authorNodeBuilder = $authorNodeDefinition->children();
        $authorNodeBuilder->scalarNode('name')->cannotBeEmpty();
        $authorNodeBuilder->scalarNode('email')->cannotBeEmpty();
        $authorNodeBuilder->scalarNode('homepage')->cannotBeEmpty();
        $authorNodeBuilder->scalarNode('role')->cannotBeEmpty();
    }

    /**
     * @param ArrayNodeDefinition $rootNodeDefinition
     */
    private function addOptionalSettingsList(ArrayNodeDefinition $rootNodeDefinition)
    {
        $settingsNodeDefinition = $rootNodeDefinition->children()->arrayNode('settings');
        $settingsNodeDefinition
            ->requiresAtLeastOneElement()
            ->performNoDeepMerging()
        ;

        /** @var ArrayNodeDefinition $settingNodeDefinition */
        $settingNodeDefinition = $settingsNodeDefinition->prototype('array');
        $settingNodeDefinition
            ->validate()
            ->ifTrue(function ($setting) {
                return [] === $setting;
            })
            ->thenInvalid('Setting cannot be empty!')
        ;

        $settingNodeBuilder = $settingNodeDefinition->children();
        $settingNodeBuilder->scalarNode('name')->cannotBeEmpty();
        $settingNodeBuilder->scalarNode('label')->defaultNull();
        $settingNodeBuilder->scalarNode('value')->cannotBeEmpty();
        $settingNodeBuilder->scalarNode('type')->cannotBeEmpty();
        $settingNodeBuilder->scalarNode('help')->defaultNull();
        $optionsNodeDefinition = $settingNodeBuilder->arrayNode('options');
        $optionsChildren = $optionsNodeDefinition->arrayPrototype()->children();
        $optionsChildren->scalarNode('value')->cannotBeEmpty();
        $optionsChildren->scalarNode('label')->cannotBeEmpty();
    }
}
