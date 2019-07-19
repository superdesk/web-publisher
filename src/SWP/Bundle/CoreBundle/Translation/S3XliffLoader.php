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
use SWP\Bundle\CoreBundle\Theme\Provider\CachedThemeAssetProviderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Loader\LoaderInterface;

class S3XliffLoader implements LoaderInterface
{
    protected $themeAssetProvider;

    public function __construct(CachedThemeAssetProviderInterface $themeAssetProvider)
    {
        $this->themeAssetProvider = $themeAssetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $resource = $this->loadResource($resource);
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

    private function loadResource(string $resource): string
    {
        // check if is already cached
        $resourceContent = $this->themeAssetProvider->getCachedFileLocation($resource);
        if (null !== $resourceContent) {
            return $resourceContent;
        }

        // if it's not theme file - use original resource path
        if (false === strpos($resource, 'app/themes/')) {
            return $resource;
        }

        // download resource and store in cache
        $this->themeAssetProvider->readFile($resource);

        // return cached path
        return $this->themeAssetProvider->getCachedFileLocation($resource);
    }
}
