<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Bundle\UserBundle\Form\RegistrationFormType;
use SWP\Bundle\UserBundle\Model\ResetPasswordRequest;
use SWP\Bundle\UserBundle\Model\User;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('swp_user');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('from_email')
                    ->isRequired()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->arrayNode('user')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(User::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('interface')->defaultValue(UserInterface::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('reset_password_request')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(ResetPasswordRequest::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('interface')->defaultValue(UserInterface::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                ->end() // classes
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addRegistrationSection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function addRegistrationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('confirmation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('template')->defaultValue('@SWPUser/Registration/confirmation_email.html.twig')->end()
                                ->arrayNode('from_email')
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                        ->end()
                    ->end()
                    ->arrayNode('form')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('type')->defaultValue(RegistrationFormType::class)->end()
                            ->scalarNode('name')->defaultValue('swp_user_registration_form')->end()
                            ->arrayNode('validation_groups')
                                ->prototype('scalar')->end()
                                ->defaultValue(['Registration', 'Default'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->end();
    }
}
