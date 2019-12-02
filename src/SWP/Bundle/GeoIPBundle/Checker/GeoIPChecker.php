<?php

declare(strict_types=1);

namespace SWP\Bundle\GeoIPBundle\Checker;

use GeoIp2\Exception\AddressNotFoundException;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\GeoIPBundle\Reader\ReaderInterface;

class GeoIPChecker
{
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function isGranted(string $ipAddress, ArticleInterface $article): bool
    {
        $place = $article->getPlace();

        if (!isset($place['country'])) {
            return false;
        }

        try {
            return $place['country'] === $this->reader->getCountry($ipAddress);
        } catch (AddressNotFoundException $e) {
            return false;
        }
    }
}
