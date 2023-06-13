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

namespace SWP\Bundle\CoreBundle\Model;

use DateTimeInterface;

class FailedEntry
{
    /** @var int */
    private $id;

    /** @var string */
    private $class;

    /** @var DateTimeInterface|null */
    private $failedAt;

    /** @var string|null */
    private $errorMessage;

    /** @var string */
    private $transport;

    /** @var array */
    private $redeliveries = [];

    /** @var array */
    private $message;

    /** @var string|null */
    private $exceptionStacktrace;

    public function __construct(
        int $id,
        string $class,
        ?DateTimeInterface $failedAt,
        ?string $errorMessage,
        string $transport,
        array $redeliveries = [],
        array $message = [],
        ?string $exceptionStacktrace = null
    ) {
        $this->id = $id;
        $this->class = $class;
        $this->failedAt = $failedAt;
        $this->errorMessage = $errorMessage;
        $this->transport = $transport;
        $this->redeliveries = $redeliveries;
        $this->message = $message;
        $this->exceptionStacktrace = $exceptionStacktrace;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getFailedAt(): ?DateTimeInterface
    {
        return $this->failedAt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getTransport(): string
    {
        return $this->transport;
    }

    public function getRedeliveries(): array
    {
        return $this->redeliveries;
    }

    public function getMessage(): array
    {
        return $this->message;
    }

    public function getExceptionStacktrace(): ?string
    {
        return $this->exceptionStacktrace;
    }
}
