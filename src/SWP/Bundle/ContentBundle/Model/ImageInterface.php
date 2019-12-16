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
    public const VARIANT_WEBP = 'webp';

    public function getWidth(): int;

    public function getHeight(): int;

    public function getLength(): float;

    public function setHeight(int $height): void;

    public function setWidth(int $width): void;

    public function setLength(float $length): void;

    public function getVariants(): array;

    public function setVariants(array $variants): void;

    public function addVariant(string $variant): void;

    public function hasVariant(string $variant): bool;
}
