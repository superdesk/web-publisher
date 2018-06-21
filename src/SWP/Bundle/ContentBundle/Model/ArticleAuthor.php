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

use Behat\Transliterator\Transliterator;
use SWP\Component\Bridge\Model\Author as BaseAuthor;

class ArticleAuthor extends BaseAuthor implements ArticleAuthorInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $slug;

    /**
     * @var AuthorMediaInterface
     */
    private $avatar;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        if ($name) {
            $this->setSlug($name);
        }

        parent::setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug(string $slug): void
    {
        $this->slug = Transliterator::urlize($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function setAvatar(AuthorMediaInterface $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvatar(): ?AuthorMediaInterface
    {
        return $this->avatar;
    }
}
