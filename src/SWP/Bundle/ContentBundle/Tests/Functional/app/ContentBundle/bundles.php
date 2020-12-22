<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

return [
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
    new Symfony\Bundle\MonologBundle\MonologBundle(),
    new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
    new Liip\FunctionalTestBundle\LiipFunctionalTestBundle(),
    new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new JMS\SerializerBundle\JMSSerializerBundle(),
    new FOS\RestBundle\FOSRestBundle(),
    new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
    new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
    new Oneup\FlysystemBundle\OneupFlysystemBundle(),
    new SWP\Bundle\StorageBundle\SWPStorageBundle(),
    new Knp\Bundle\MenuBundle\KnpMenuBundle(),
    new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
    new Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle(),
    new Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle(),
    new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
    new Sentry\SentryBundle\SentryBundle(),

    new SWP\Bundle\TemplatesSystemBundle\SWPTemplatesSystemBundle(),
    new SWP\Bundle\BridgeBundle\SWPBridgeBundle(),
    new SWP\Bundle\MenuBundle\SWPMenuBundle(),
    new SWP\Bundle\SeoBundle\SWPSeoBundle(),
    new SWP\Bundle\RedirectRouteBundle\SWPRedirectRouteBundle(),
    new SWP\Bundle\ContentBundle\SWPContentBundle(),
];
