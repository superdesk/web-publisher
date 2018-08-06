<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

interface ExternalDataInterface extends PackageAwareInterface
{
    public function getKey(): string;

    public function setKey(string $key): void;

    public function getValue(): string;

    public function setValue(string $value): void;
}
