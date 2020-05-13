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

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

class ComponentLayouts
{
    /** @var ComponentLayout|null */
    private $halfMarginBelowLayout;

    /** @var ComponentLayout|null */
    private $marginBetweenComponents;

    public function getHalfMarginBelowLayout(): ?ComponentLayout
    {
        return $this->halfMarginBelowLayout;
    }

    public function setHalfMarginBelowLayout(?ComponentLayout $halfMarginBelowLayout): void
    {
        $this->halfMarginBelowLayout = $halfMarginBelowLayout;
    }

    public function getMarginBetweenComponents(): ?ComponentLayout
    {
        return $this->marginBetweenComponents;
    }

    public function setMarginBetweenComponents(?ComponentLayout $marginBetweenComponents): void
    {
        $this->marginBetweenComponents = $marginBetweenComponents;
    }
}
