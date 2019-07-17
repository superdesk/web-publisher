<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\Translation;

use JMS\TranslationBundle\Exception\RuntimeException;
use League\Flysystem\Filesystem;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Loader\LoaderInterface;

class S3XliffLoader implements LoaderInterface
{
    protected $filesystem;

    protected $cacheDir;

    public function __construct(Filesystem $filesystem, string $cacheDir)
    {
        $this->filesystem = $filesystem;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $resource = $this->cacheLoad($resource);
        $previous = libxml_use_internal_errors(true);
        if (false === $xml = simplexml_load_file($resource)) {
            libxml_use_internal_errors($previous);
            $error = libxml_get_last_error();

            throw new RuntimeException(sprintf('An error occurred while reading "%s": %s', $resource, $error->message));
        }

        libxml_use_internal_errors($previous);

        $xml->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');

        $catalogue = new MessageCatalogue($locale);
        foreach ($xml->xpath('//xliff:trans-unit') as $translation) {
            $id = ($resName = (string) $translation->attributes()->resname)
                ? $resName : (string) $translation->source;

            $catalogue->set($id, (string) $translation->target, $domain);
        }

        $catalogue->addResource(new FileResource($resource));

        return $catalogue;
    }

    private function cacheLoad(string $resource): string
    {
        $cacheFilePath = $this->cacheDir.DIRECTORY_SEPARATOR.'s3'.DIRECTORY_SEPARATOR.$resource;
        $localFilesystem = new SymfonyFilesystem();
        if ($localFilesystem->exists($cacheFilePath)) {
            return $cacheFilePath;
        }

        if (null === strpos($resource, 'app/themes/') || !$this->filesystem->has($resource)) {
            return $resource;
        }

        $file = $this->filesystem->get($resource);
        $localFilesystem->dumpFile($cacheFilePath, $file->read());

        return $cacheFilePath;
    }
}
