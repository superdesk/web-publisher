<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

class LinkedArticle
{
    public const RELATIONSHIP_RELATED = 'related';

    public const RELATIONSHIP_PROMOTED = 'promoted';

    /** @var string */
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
