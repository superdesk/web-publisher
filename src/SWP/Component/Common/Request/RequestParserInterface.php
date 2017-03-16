<?php

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Request;

interface RequestParserInterface
{
    /**
     * @param array $requestLinks
     *
     * @return array
     */
    public static function getNotConvertedLinks(array $requestLinks);
}
