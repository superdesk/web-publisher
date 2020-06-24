<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

class Place implements PlaceInterface
{
    /** @var int */
    protected $id;

    /** @var string|null */
    protected $country;

    /** @var string|null */
    protected $worldRegion;

    /** @var string|null */
    protected $state;

    /** @var string|null */
    protected $qcode;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $qgroup;

    /** @var MetadataInterface|null */
    protected $metadata;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    public function setMetadata(?MetadataInterface $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getWorldRegion(): ?string
    {
        return $this->worldRegion;
    }

    public function setWorldRegion(?string $worldRegion): void
    {
        $this->worldRegion = $worldRegion;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getQcode(): ?string
    {
        return $this->qcode;
    }

    public function setQcode(?string $qcode): void
    {
        $this->qcode = $qcode;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getGroup(): ?string
    {
        return $this->qgroup;
    }

    public function setGroup(?string $group): void
    {
        $this->qgroup = $group;
    }
}
