<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Validator;

interface ValidatorOptionsInterface
{
    /**
     * Gets current validator schema.
     *
     * @return string
     */
    public function getSchema();

    /**
     * Gets validator format.
     *
     * @return string
     */
    public function getFormat();
}
