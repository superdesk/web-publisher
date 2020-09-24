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

interface ArticleExtraEmbedFieldInterface extends ArticleExtraFieldInterface
{

    public function setEmbed(?string $embed);

    public function getEmbed(): ?string;

    public function setDescription(?string $description);

    public function getDescription(): ?string;

}