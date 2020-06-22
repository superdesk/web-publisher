<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface ServiceInterface extends PersistableInterface
{
    public function getCode(): string;

    public function setCode(string $code): void;

    public function getMetadata(): ?MetadataInterface;

    public function setMetadata(?MetadataInterface $metadata): void;
}
