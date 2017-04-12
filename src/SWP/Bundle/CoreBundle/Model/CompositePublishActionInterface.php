<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

interface CompositePublishActionInterface
{
    /**
     * @return array
     */
    public function getDestinations(): array;

    /**
     * @param DestinationInterface[] $destinations
     */
    public function setDestinations(array $destinations);
}
