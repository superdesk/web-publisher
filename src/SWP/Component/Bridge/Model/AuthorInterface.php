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
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getRole(): ?string;

    public function setRole(?string $role): void;

    public function setBiography(?string $biography): void;

    public function getBiography(): ?string;

    public function getJobTitle(): array;

    public function setJobTitle(array $jobTitle): void;

    public function getAvatarUrl(): ?string;

    public function setAvatarUrl(?string $avatarUrl);
}
