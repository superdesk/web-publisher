<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as SymNotFoundHttpException;


class NotFoundHttpException extends SymNotFoundHttpException {
}
