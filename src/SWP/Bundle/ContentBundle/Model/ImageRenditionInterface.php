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

/**
 * Interface ImageRenditionInterface.
 */
interface ImageRenditionInterface extends PreviewUrlAwareInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    public function getName(): string;

    public function setImage(ImageInterface $image): void;

    public function getImage(): ImageInterface;

    public function setMedia(ArticleMediaInterface $media): void;

    public function setHeight(int $height): void;

    public function setWidth(int $width): void;

    public function setName(string $name): void;

    public function isConvertedToWebp(): bool;
}
