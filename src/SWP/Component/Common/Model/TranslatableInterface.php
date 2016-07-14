<?php

/**
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Common\Model;

interface TranslatableInterface
{
    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param $locale
     */
    public function setLocale($locale);
}
