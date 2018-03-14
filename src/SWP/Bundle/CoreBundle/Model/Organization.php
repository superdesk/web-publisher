<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Component\MultiTenancy\Model\Organization as BaseOrganization;

class Organization extends BaseOrganization implements SettingsOwnerInterface
{
    /**
     * @var array
     */
    protected $publishDestinations = [];

    public function __construct()
    {
        parent::__construct();

        $this->publishDestinations = new ArrayCollection();
    }

    public function getPublishDestinations()
    {
        return $this->publishDestinations;
    }

    public function setPublishDestinations(Collection $publishDestinations): void
    {
        $this->publishDestinations = $publishDestinations;
    }

    /**
     * {@inheritdoc}
     */
    public function addPublishDestination(PublishDestinationInterface $publishDestination): void
    {
        if (!$this->hasPublishDestination($publishDestination)) {
            $this->publishDestinations->add($publishDestination);
            $publishDestination->setOrganization($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePublishDestination(PublishDestinationInterface $publishDestination): void
    {
        if ($this->hasPublishDestination($publishDestination)) {
            $this->publishDestinations->removeElement($publishDestination);
            $publishDestination->setOrganization(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPublishDestination(PublishDestinationInterface $publishDestination): bool
    {
        return $this->publishDestinations->contains($publishDestination);
    }
}
