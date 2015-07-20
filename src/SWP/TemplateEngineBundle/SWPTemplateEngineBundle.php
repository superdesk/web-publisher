<?php

namespace SWP\TemplateEngineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SWP\TemplateEngineBundle\DependencyInjection\ContainerBuilder\MetaLoaderCompilerPass;

class SWPTemplateEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetaLoaderCompilerPass());
    }
}
