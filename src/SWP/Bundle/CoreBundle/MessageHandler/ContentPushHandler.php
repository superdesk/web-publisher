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

namespace SWP\Bundle\CoreBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository;
use SWP\Bundle\CoreBundle\Hydrator\PackageHydratorInterface;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMessage;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ContentPushHandler implements MessageHandlerInterface
{
    public function __invoke(ContentPushMessage $contentPushMessage)
    {
        $content = $contentPushMessage->getContent();
        $tenantId = $contentPushMessage->getTenantId();
        $package = $this->jsonToPackageTransformer->transform($content);

        $this->execute($tenantId, $package);
    }
}
