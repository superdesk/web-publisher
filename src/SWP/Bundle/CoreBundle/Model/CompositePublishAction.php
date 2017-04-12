<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

class CompositePublishAction implements CompositePublishActionInterface
{
    /**
     * @var PublishDestinationInterface[]
     */
    private $destinations = [];

    /**
     * CompositePublishAction constructor.
     *
     * @param PublishDestinationInterface[] $destinations
     */
    public function __construct(array $destinations = [])
    {
        $this->destinations = $destinations;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestinations(): array
    {
        return $this->destinations;
    }

    /**
     * {@inheritdoc}
     */
    public function setDestinations(array $destinations)
    {
        $this->destinations = $destinations;
    }
}
