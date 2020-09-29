<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

class ArticleExtraTextField extends ArticleExtraField implements ArticleExtraTextFieldInterface
{
    /** @var string */
    protected $value;

    private function __construct(string $key, string $value)
    {
        $this->setFieldName($key);
        $this->setValue($value);
    }

    public static function newFromValue(string $key, string $value): ArticleExtraTextField
    {
        return new self($key, $value);
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function toApiFormat(): ?string
    {
        return $this->value;
    }
}
