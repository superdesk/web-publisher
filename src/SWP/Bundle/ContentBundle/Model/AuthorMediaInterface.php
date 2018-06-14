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
use SWP\Component\Storage\Model\PersistableInterface;

interface AuthorMediaInterface extends PersistableInterface
{
    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId();

    /**
     * Get the value of key
     *
     * @return  string
     */
    public function getKey();

    /**
     * Set the value of key
     *
     * @param  string  $key
     */
    public function setKey(string $key);

    /**
     * Get the value of file
     *
     * @return  FileInterface
     */
    public function getFile();

    /**
     * Set the value of file
     *
     * @param  FileInterface  $file
     */
    public function setFile(FileInterface $file);

    /**
     * Get the value of image
     *
     * @return  ImageInterface
     */
    public function getImage();

    /**
     * Set the value of image
     *
     * @param  ImageInterface  $image
     */
    public function setImage(ImageInterface $image);

    /**
     * Get the value of author
     *
     * @return  AuthorInterface
     */
    public function getAuthor();

    /**
     * Set the value of author
     *
     * @param  AuthorInterface  $author
     */
    public function setAuthor(AuthorInterface $author);
}