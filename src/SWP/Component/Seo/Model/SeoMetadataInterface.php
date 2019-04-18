<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SeoMetadataInterface extends PersistableInterface, TimestampableInterface
{
    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getTags(): array;

    public function setTags(array $tags): void;

    public function getAuthors(): array;

    public function setAuthors(array $authors): void;
}
