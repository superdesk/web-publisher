<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

class Subject implements SubjectInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $code;

    /** @var string */
    protected $scheme;

    /** @var MetadataInterface|null */
    protected $metadata;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }

    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    public function setMetadata(?MetadataInterface $metadata): void
    {
        $this->metadata = $metadata;
    }
}
