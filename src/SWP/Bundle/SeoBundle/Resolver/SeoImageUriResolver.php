<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Resolver;

use League\Flysystem\Filesystem;
use SWP\Component\Seo\Model\SeoMetadataInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class SeoImageUriResolver implements SeoImageUriResolverInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function resolveUri(SeoMetadataInterface $object, string $fieldName): string
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $value = $propertyAccessor->getValue($object, $fieldName);

        if (!$this->filesystem->has($value)) {
            return '';
        }

        $path = $this->filesystem->get($value)->getPath();

        return '/'.$path;

//        return $this->router->generate(
//            'swp_theme_logo_get',
//            [
//                'id' => $setting,
//            ],
//            UrlGeneratorInterface::RELATIVE_PATH
//        );
    }
}
