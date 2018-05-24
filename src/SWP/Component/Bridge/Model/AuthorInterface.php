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

interface AuthorInterface
{
    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return null|string
     */
    public function getRole(): ?string;

    /**
     * @param null|string $role
     */
    public function setRole(?string $role): void;

    /**
     * @param null|string $biography
     */
    public function setBiography(?string $biography): void;

    /**
     * @return null|string
     */
    public function getBiography(): ?string;

    /**
     * @return array
     */
    public function getJobTitle(): array;

    /**
     * @param array $jobTitle
     */
    public function setJobTitle(array $jobTitle): void;

    /**
     * @return null|string
     */
    public function getAvatarUrl(): ?string;

    /**
     * @param null|string $avatarUrl
     *
     * @return mixed
     */
    public function setAvatarUrl(?string $avatarUrl);
}
