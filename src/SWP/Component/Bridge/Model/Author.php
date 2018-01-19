<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
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

class Author implements AuthorInterface
{
    protected $id;

    protected $name;

    protected $role;

    protected $jobTitle = [];

    protected $biography;

    protected $package;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public function getJobTitle(): array
    {
        return $this->jobTitle;
    }

    public function setJobTitle(array $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    public function setBiography(?string $biography): void
    {
        $this->biography = $biography;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
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
