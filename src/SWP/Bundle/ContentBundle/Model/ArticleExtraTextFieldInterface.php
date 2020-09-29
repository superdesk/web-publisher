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

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

interface ArticleExtraTextFieldInterface extends ArticleExtraFieldInterface
{
    public function setValue(string $value): void;

    public function getValue(): ?string;

    public static function newFromValue(string $key, string $value): ArticleExtraTextField;
}
