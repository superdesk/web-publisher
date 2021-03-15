<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */


namespace SWP\Bundle\CoreBundle\Adapter;


use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;

/**
 * Class PwaAdapter
 * @package SWP\Bundle\CoreBundle\Adapter
 *
 * email confirmation url to be pointing to pwa instance, not to publisher
 * config for firebase push messages
 */
class PwaAdapter implements AdapterInterface
{

    public function create(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        // TODO: Implement create() method.
    }

    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        // TODO: Implement update() method.
    }

    public function publish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        // TODO: Implement publish() method.
    }

    public function unpublish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        // TODO: Implement unpublish() method.
    }

    public function supports(OutputChannelInterface $outputChannel): bool
    {
        return OutputChannelInterface::TYPE_PWA === $outputChannel->getType();
    }
}