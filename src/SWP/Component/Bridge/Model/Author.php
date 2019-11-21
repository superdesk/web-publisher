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
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var array
     */
    protected $jobTitle = [];

    /**
     * @var string
     */
    protected $biography;

    /**
     * @var string
     */
    protected $avatarUrl;

    /**
     * @var string|null
     */
    protected $twitter;

    /**
     * @var string|null
     */
    protected $instagram;

    /**
     * @var string|null
     */
    protected $facebook;

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

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): void
    {
        $this->twitter = $twitter;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): void
    {
        $this->instagram = $instagram;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): void
    {
        $this->facebook = $facebook;
    }
}
