<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

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
