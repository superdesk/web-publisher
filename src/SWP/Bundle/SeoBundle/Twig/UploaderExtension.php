<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Twig;

use SWP\Bundle\SeoBundle\Resolver\SeoImageUriResolverInterface;
use SWP\Component\Seo\Model\SeoMetadataInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UploaderExtension extends AbstractExtension
{
    /**
     * @var SeoImageUriResolverInterface
     */
    private $seoImageUriResolver;

    public function __construct(SeoImageUriResolverInterface $seoImageUriResolver)
    {
        $this->seoImageUriResolver = $seoImageUriResolver;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seo_image', [$this, 'getSeoImage']),
        ];
    }

    public function getSeoImage(SeoMetadataInterface $object, string $fieldName): string
    {
        return $this->seoImageUriResolver->resolveUri($object, $fieldName);
    }
}
