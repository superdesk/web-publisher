<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Processor;

interface RulesProcessorInterface
{
    public const KEY_ORGANIZATION = 'organization';

    public const KEY_TENANTS = 'tenants';

    public const KEY_TENANT = 'tenant';

    public const KEY_ROUTE = 'route';

    public const KEY_FBIA = 'is_published_fbia';

    public const KEY_PUBLISHED = 'published';

    public const KEY_PAYWALL_SECURED = 'paywall_secured';

    public const KEY_APPLE_NEWS = 'is_published_to_apple_news';

    /**
     * @param array $evaluatedRules
     *
     * @return array
     */
    public function process(array $evaluatedRules): array;
}
