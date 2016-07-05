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
            new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new EmanueleMinotto\TwigCacheBundle\TwigCacheBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),

            new SWP\Bundle\MultiTenancyBundle\SWPMultiTenancyBundle(),
            new SWP\Bundle\TemplateEngineBundle\SWPTemplateEngineBundle(),
            new SWP\Bundle\WebRendererBundle\SWPWebRendererBundle(),
            new SWP\UpdaterBundle\SWPUpdaterBundle(),
            new SWP\Bundle\BridgeBundle\SWPBridgeBundle(),
            new SWP\Bundle\ContentBundle\SWPContentBundle(),
            new SWP\Bundle\AnalyticsBundle\SWPAnalyticsBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new SWP\Bundle\FixturesBundle\SWPFixturesBundle();
        }

        if (in_array($this->getEnvironment(), ['test'])) {
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
