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
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getJobTitle(): array
    {
        return $this->jobTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setJobTitle(array $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setBiography(?string $biography): void
    {
        if (\is_string($biography) && \strlen($biography) > 460) {
            $biography = mb_substr($biography, 0, 460);
        }

        $this->biography = $biography;
    }

    /**
     * {@inheritdoc}
     */
    public function getBiography(): ?string
    {
        return $this->biography;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvatarUrl(?string $avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }
}
