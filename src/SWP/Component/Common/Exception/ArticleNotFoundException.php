<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Exception;

class ArticleNotFoundException extends \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
{
}
