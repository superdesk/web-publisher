<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Bridge\Model\AuthorInterface as BaseAuthorInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ArticleAuthorInterface extends BaseAuthorInterface, PersistableInterface
{
    /**
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void;

    /**
     * @return AuthorMediaInterface
     */
    public function getAvatar(): AuthorMediaInterface;

    /**
     * @param AuthorMediaInterface $avatar
     */
    public function setAvatar(AuthorMediaInterface $avatar): void;
}
