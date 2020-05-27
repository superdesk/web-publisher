<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

use Symfony\Component\Serializer\Annotation\SerializedName;

class LinkedArticle
{
    public const RELATIONSHIP_RELATED = 'related';

    public const RELATIONSHIP_PROMOTED = 'promoted';

    /**
     * @SerializedName("URL")
     *
     * @var string
     */
    private $url;

    /** @var string */
    private $relationship;

    public function __construct(string $url, string $relationship)
    {
        $this->url = $url;
        $this->relationship = $relationship;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRelationship(): string
    {
        return $this->relationship;
    }
}
