<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\StorageBundle\DependencyInjection\Bundle;

use SWP\Component\Storage\Bundle\BundleInterface;
use SWP\Bundle\StorageBundle\Drivers;
use SWP\Component\Storage\Exception\InvalidDriverException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
abstract class Bundle extends BaseBundle implements BundleInterface
{
    /**
     * Default format of mapping files.
     *
     * @var string
     */
    protected $mappingFormat = BundleInterface::MAPPING_YAML;

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        if (null !== $this->getModelClassNamespace()) {
            foreach ($this->getSupportedDrivers() as $driver) {
                list($compilerPassClassName, $compilerPassMethod) = $this->getMappingCompilerPassInfo($driver);
                if (class_exists($compilerPassClassName)) {
                    if (!method_exists($compilerPassClassName, $compilerPassMethod)) {
                        throw new InvalidConfigurationException(
                            "The 'mappingFormat' value is invalid, must be 'xml', 'yml' or 'annotation'."
                        );
                    }

                    switch ($this->mappingFormat) {
                        case BundleInterface::MAPPING_XML:
                        case BundleInterface::MAPPING_YAML:
                            $container->addCompilerPass($compilerPassClassName::$compilerPassMethod(
                                [$this->getConfigFilesPath($driver) => $this->getModelClassNamespace()],
                                [sprintf('%s.persistence.manager_name', $this->getBundlePrefix())],
                                sprintf('%s.backend_type_%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        case BundleInterface::MAPPING_ANNOTATION:
                            $container->addCompilerPass($compilerPassClassName::$compilerPassMethod(
                                [$this->getModelClassNamespace()],
                                [$this->getConfigFilesPath($driver)],
                                [sprintf('%s.persistence.manager_name', $this->getBundlePrefix())],
                                sprintf('%s.backend_type_%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                    }
                }
            }
        }
    }

    /**
     * Return the prefix of the bundle.
     *
     * @return string
     */
    protected function getBundlePrefix()
    {
        return Container::underscore(substr(strrchr(get_class($this), '\\'), 1, -6));
    }

    /**
     * Return the model namespace.
     *
     * @return string|null
     */
    protected function getModelClassNamespace()
    {
        return;
    }

    /**
     * Return mapping compiler pass class depending on driver.
     *
     * @param string $driverType
     *
     * @return array
     *
     * @throws InvalidDriverException
     */
    protected function getMappingCompilerPassInfo($driverType)
    {
        switch ($driverType) {
            case Drivers::DRIVER_DOCTRINE_MONGODB_ODM:
                $mappingsPassClassName = 'Doctrine\\Bundle\\MongoDBBundle\\DependencyInjection\\Compiler\\DoctrineMongoDBMappingsPass';
                break;
            case Drivers::DRIVER_DOCTRINE_ORM:
                $mappingsPassClassName = 'Doctrine\\Bundle\\DoctrineBundle\\DependencyInjection\\Compiler\\DoctrineOrmMappingsPass';
                break;
            case Drivers::DRIVER_DOCTRINE_PHPCR_ODM:
                $mappingsPassClassName = 'Doctrine\\Bundle\\PHPCRBundle\\DependencyInjection\\Compiler\\DoctrinePhpcrMappingsPass';
                break;
            default:
                throw new InvalidDriverException($driverType);
        }

        $compilerPassMethod = sprintf('create%sMappingDriver', ucfirst($this->mappingFormat));

        return [$mappingsPassClassName, $compilerPassMethod];
    }

    /**
     * Return the absolute path where are stored the doctrine mapping.
     *
     * @param string $suffix
     *
     * @return string
     */
    protected function getConfigFilesPath($suffix)
    {
        return sprintf(
            '%s/Resources/config/doctrine-%s',
            $this->getPath(),
            strtolower($suffix)
        );
    }
}
