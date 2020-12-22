<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

interface PlaceInterface
{
    public function getCountry(): ?string;

    public function setCountry(?string $country): void;

    public function getWorldRegion(): ?string;

    public function setWorldRegion(?string $worldRegion): void;

    public function getState(): ?string;

    public function setState(?string $state): void;

    public function getQcode(): ?string;

    public function setQcode(?string $qcode): void;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getGroup(): ?string;

    public function setGroup(?string $group): void;
}
