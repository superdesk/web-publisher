<?php

/**
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Common\Model;

/**
 * EnableableInterface should be implemented by classes which needs to be
 * identified as enableable or disableable.
 */
interface EnableableInterface
{
    /**
     * Enables or disables.
     *
     * @param bool $enabled Whether true or false
     */
    public function setEnabled($enabled);

    /**
     * Checks whether the object is enabled or disabled.
     *
     * @return bool Whether true or false
     */
    public function isEnabled();
}
