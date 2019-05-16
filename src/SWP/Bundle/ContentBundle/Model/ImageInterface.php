<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

interface ImageInterface extends FileInterface
{
    /**
     * Get the value of Width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the value of Height.
     *
     * @return int
     */
    public function getHeight();

    public function setHeight(int $height): self;

    public function setWidth(int $width): self;
}
