<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Redirect Route Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RedirectRouteBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface RedirectRouteInterface extends TimestampableInterface, PersistableInterface
{
}
