<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Resolver;

use SWP\Component\Seo\Model\SeoMetadataInterface;

interface SeoImageUriResolverInterface
{
    public function resolveUri(SeoMetadataInterface $object, string $fieldName): string;
}
