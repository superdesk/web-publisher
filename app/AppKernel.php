<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new EmanueleMinotto\TwigCacheBundle\TwigCacheBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new Burgov\Bundle\KeyValueFormBundle\BurgovKeyValueFormBundle(),
            new Takeit\Bundle\AmpHtmlBundle\TakeitAmpHtmlBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            new SWP\Bundle\StorageBundle\SWPStorageBundle(),
            new SWP\Bundle\MultiTenancyBundle\SWPMultiTenancyBundle(),
            new SWP\Bundle\TemplatesSystemBundle\SWPTemplatesSystemBundle(),
            new SWP\Bundle\BridgeBundle\SWPBridgeBundle(),
            new SWP\Bundle\ContentBundle\SWPContentBundle(),
            new SWP\Bundle\AnalyticsBundle\SWPAnalyticsBundle(),
            new SWP\Bundle\RuleBundle\SWPRuleBundle(),
            new SWP\Bundle\MenuBundle\SWPMenuBundle(),
            new SWP\Bundle\ContentListBundle\SWPContentListBundle(),
            new SWP\Bundle\FacebookInstantArticlesBundle\SWPFacebookInstantArticlesBundle(),
            new SWP\Bundle\RevisionBundle\SWPRevisionBundle(),
            new SWP\Bundle\CoreBundle\SWPCoreBundle(),

            new Sentry\SentryBundle\SentryBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new SWP\Bundle\FixturesBundle\SWPFixturesBundle();
            $bundles[] = new Pixers\DoctrineProfilerBundle\PixersDoctrineProfilerBundle();
        }

        if (in_array($this->getEnvironment(), ['test'])) {
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
