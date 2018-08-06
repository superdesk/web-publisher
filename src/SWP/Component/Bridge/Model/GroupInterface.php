<?php

declare(strict_types=1);

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface GroupInterface extends PersistableInterface, SoftDeletableInterface
{
    public function getCode(): string;

    public function setCode(string $code): void;

    public function getItems(): Collection;

    public function setItems(Collection $items): void;

    public function getPackage(): PackageInterface;

    public function setPackage(PackageInterface $package): void;
}
