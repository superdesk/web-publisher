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

use SWP\Component\Bridge\Model\AuthorInterface;
use SWP\Component\Common\Model\TimestampableTrait;

/**
 * AuthorMedia represents media which belongs to Author.
 */
class AuthorMedia implements AuthorMediaInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @var ImageInterface
     */
    protected $image;

    /**
     * @var AuthorInterface
     */
    protected $author;

    /**
     * AuthorMedia constructor.
     *
     * @param string              $key
     * @param AuthorInterface     $author
     * @param ImageInterface|null $image
     */
    public function __construct(string $key, AuthorInterface $author, ImageInterface $image = null)
    {
        $this->setKey($key);
        $this->setAuthor($author);
        if (null !== $image) {
            $this->setImage($image);
        }
        $this->setCreatedAt(new \DateTime());
    }

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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor(AuthorInterface $author)
    {
        $this->author = $author;
    }
}
