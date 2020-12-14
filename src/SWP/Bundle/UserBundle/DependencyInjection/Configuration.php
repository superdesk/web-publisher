<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
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
            ->end() // classes
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        $this->addProfileSection($treeBuilder->getRootNode());
        $this->addChangePasswordSection($treeBuilder->getRootNode());
        $this->addRegistrationSection($treeBuilder->getRootNode());
        $this->addResettingSection($treeBuilder->getRootNode());
        $this->addServiceSection($treeBuilder->getRootNode());
        $this->addGroupSection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function addProfileSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('profile')
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
            ->arrayNode('form')
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('validation_group')
            ->children()
            ->scalarNode('type')->defaultValue(Type\ProfileFormType::class)->end()
            ->scalarNode('name')->defaultValue('fos_user_profile_form')->end()
            ->arrayNode('validation_groups')
            ->prototype('scalar')->end()
            ->defaultValue(['Profile', 'Default'])
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
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
            ->scalarNode('template')->defaultValue('@FOSUser/Registration/email.txt.twig')->end()
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
            ->scalarNode('type')->defaultValue(Type\RegistrationFormType::class)->end()
            ->scalarNode('name')->defaultValue('fos_user_registration_form')->end()
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

    private function addResettingSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('resetting')
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
            ->scalarNode('retry_ttl')->defaultValue(7200)->end()
            ->scalarNode('token_ttl')->defaultValue(86400)->end()
            ->arrayNode('email')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('template')->defaultValue('@FOSUser/Resetting/email.txt.twig')->end()
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
            ->scalarNode('type')->defaultValue(Type\ResettingFormType::class)->end()
            ->scalarNode('name')->defaultValue('fos_user_resetting_form')->end()
            ->arrayNode('validation_groups')
            ->prototype('scalar')->end()
            ->defaultValue(['ResetPassword', 'Default'])
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    private function addChangePasswordSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('change_password')
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
            ->arrayNode('form')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('type')->defaultValue(Type\ChangePasswordFormType::class)->end()
            ->scalarNode('name')->defaultValue('fos_user_change_password_form')->end()
            ->arrayNode('validation_groups')
            ->prototype('scalar')->end()
            ->defaultValue(['ChangePassword', 'Default'])
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('service')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('mailer')->defaultValue('fos_user.mailer.default')->end()
            ->scalarNode('email_canonicalizer')->defaultValue('fos_user.util.canonicalizer.default')->end()
            ->scalarNode('token_generator')->defaultValue('fos_user.util.token_generator.default')->end()
            ->scalarNode('username_canonicalizer')->defaultValue('fos_user.util.canonicalizer.default')->end()
            ->scalarNode('user_manager')->defaultValue('fos_user.user_manager.default')->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    private function addGroupSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('group')
            ->canBeUnset()
            ->children()
            ->scalarNode('group_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('group_manager')->defaultValue('fos_user.group_manager.default')->end()
            ->arrayNode('form')
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('validation_group')
            ->children()
            ->scalarNode('type')->defaultValue(Type\GroupFormType::class)->end()
            ->scalarNode('name')->defaultValue('fos_user_group_form')->end()
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
