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

use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\AuthorInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param string $key
     * @param AuthorInterface $author
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
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of key
     *
     * @return  string
     */ 
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the value of key
     *
     * @param  string  $key
     */ 
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Get the value of file
     *
     * @return  FileInterface
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @param  FileInterface  $file
     */ 
    public function setFile(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Get the value of image
     *
     * @return  ImageInterface
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @param  ImageInterface  $image
     */ 
    public function setImage(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * Get the value of author
     *
     * @return  AuthorInterface
     */ 
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @param  AuthorInterface  $author
     */ 
    public function setAuthor(AuthorInterface $author)
    {
        $this->author = $author;
    }
}