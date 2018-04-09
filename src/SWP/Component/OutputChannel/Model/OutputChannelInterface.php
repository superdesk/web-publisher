<?php

declare(strict_types=1);

namespace SWP\Component\OutputChannel\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface OutputChannelInterface extends PersistableInterface
{
    public const TYPE_WORDPRESS = 'wordpress';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param array $config
     */
    public function setConfig(array $config): void;
}
