<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle;

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SWPContentBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $this->buildPhpcrCompilerPass($container);
        $this->buildOrmCompilerPass($container);
    }

    /**
     * Creates and registers compiler passes for PHPCR-ODM mapping if both the
     * phpcr-odm and the phpcr-bundle are present.
     *
     * @param ContainerBuilder $container
     */
    private function buildPhpcrCompilerPass(ContainerBuilder $container)
    {
        if (!class_exists('Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass')
            || !class_exists('Doctrine\ODM\PHPCR\Version')
        ) {
            return;
        }

        $container->addCompilerPass(
            DoctrinePhpcrMappingsPass::createYamlMappingDriver(
                array(
                    realpath(__DIR__.'/Resources/config/doctrine-model') => 'SWP\Bundle\ContentBundle\Model',
                    realpath(__DIR__.'/Resources/config/doctrine-phpcr') => 'SWP\Bundle\ContentBundle\Doctrine\Phpcr',
                ),
                array('swp_content.dynamic.persistence.phpcr.manager_name'),
                'swp_content.backend_type_phpcr',
                array('SWPContentBundle' => 'SWP\Bundle\ContentBundle\Doctrine\Phpcr')
            )
        );
    }

    /**
     * Creates and registers compiler passes for ORM mappings if both doctrine
     * ORM and a suitable compiler pass implementation are available.
     *
     * @param ContainerBuilder $container
     */
    private function buildOrmCompilerPass(ContainerBuilder $container)
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            return;
        }

        $doctrineOrmCompiler = $this->findDoctrineOrmCompiler();
        if (!$doctrineOrmCompiler) {
            return;
        }

        $container->addCompilerPass(
            $doctrineOrmCompiler::createYamlMappingDriver(
                array(
                    realpath(__DIR__.'/Resources/config/doctrine-model') => 'SWP\Bundle\ContentBundle\Model',
                    realpath(__DIR__.'/Resources/config/doctrine-orm') => 'SWP\Bundle\ContentBundle\Doctrine\Orm',
                ),
                array('swp_content.dynamic.persistence.orm.manager_name'),
                'cmf_routing.backend_type_orm',
                array('SWPContentBundle' => 'SWP\Bundle\ContentBundle\Doctrine\Orm')
            )
        );
    }

    /**
     * Looks for a mapping compiler pass. If available, use the one from
     * DoctrineBundle (available only since DoctrineBundle 2.4 and Symfony 2.3).
     *
     * @return bool|string the compiler pass to use or false if no suitable
     *                     one was found
     */
    private function findDoctrineOrmCompiler()
    {
        if (class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass')
            && class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')
        ) {
            return 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        }

        return false;
    }
}
