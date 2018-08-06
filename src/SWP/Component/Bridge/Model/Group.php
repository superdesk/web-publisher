<?php

declare(strict_types=1);

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

    public function getPackage(): PackageInterface
    {
        return $this->package;
    }

    public function setPackage(PackageInterface $package): void
    {
        $this->package = $package;
    }
}
