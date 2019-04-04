<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface RenditionInterface extends PersistableInterface, SoftDeletableInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getHref(): string;

    /**
     * @param string $href
     */
    public function setHref(string $href);

    /**
     * @return int
     */
    public function getWidth(): ?int;

    /**
     * @param int $width
     */
    public function setWidth(?int $width);

    /**
     * @return int
     */
    public function getHeight(): ?int;

    /**
     * @param int $height
     */
    public function setHeight(?int $height);

    /**
     * @return string|null
     */
    public function getMimetype(): ?string;

    /**
     * @param string|null $mimetype
     */
    public function setMimetype(?string $mimetype);

    /**
     * @return string
     */
    public function getMedia(): string;

    /**
     * @param string $media
     */
    public function setMedia(string $media);

    /**
     * @return ItemInterface
     */
    public function getItem(): ItemInterface;

    /**
     * @param ItemInterface $item
     */
    public function setItem(ItemInterface $item);
}
