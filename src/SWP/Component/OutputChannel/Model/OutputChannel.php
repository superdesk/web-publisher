<?php

declare(strict_types=1);

namespace SWP\Component\OutputChannel\Model;

class OutputChannel implements OutputChannelInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
