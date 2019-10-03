<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\SoftDeletableTrait;

class Group implements GroupInterface
{
    use SoftDeletableTrait;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var Collection
     */
    protected $items;

    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @var string|null
     */
    protected $type;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    public function getPackage(): ?PackageInterface
    {
        return $this->package;
    }

    public function setPackage(?PackageInterface $package): void
    {
        $this->package = $package;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
