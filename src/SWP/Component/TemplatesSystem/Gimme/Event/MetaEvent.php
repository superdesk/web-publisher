<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class MetaEvent extends Event
{
    private $isResultSet = false;

    private $result;

    private $data;

    private $propertyName;

    public function __construct($data, string $propertyName)
    {
        $this->data = $data;
        $this->propertyName = $propertyName;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function isResultSet(): bool
    {
        return $this->isResultSet;
    }

    public function setResult($result): void
    {
        $this->isResultSet = true;
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}
