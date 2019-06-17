<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Loader;

use League\Flysystem\FilesystemInterface;
use Sylius\Bundle\ThemeBundle\Twig\ThemeFilesystemLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

final class FilesystemTemplateLoader extends ThemeFilesystemLoader
{
    private $filesystem;

    public function __construct(
        \Twig_LoaderInterface $decoratedLoader,
        FileLocatorInterface $templateLocator,
        TemplateNameParserInterface $templateNameParser,
        FilesystemInterface $filesystem
    ) {
        parent::__construct($decoratedLoader, $templateLocator, $templateNameParser);

        $this->filesystem = $filesystem;
    }

//    public function getSourceContext($name): \Twig_Source
//    {
////        try {
//        $path = $this->findTemplate($name);
//
//        return new \Twig_Source((string) $this->filesystem->read($path), (string) $name, $path);
////        } catch (\Exception $exception) {
////            return $this->decoratedLoader->getSourceContext($name);
////        }
//    }
}
