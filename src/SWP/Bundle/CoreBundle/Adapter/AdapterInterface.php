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

namespace SWP\Bundle\CoreBundle\Adapter;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;

interface AdapterInterface
{
    /**
     * @param OutputChannelInterface $outputChannel
     * @param ArticleInterface       $article
     */
    public function create(OutputChannelInterface $outputChannel, ArticleInterface $article): void;

    /**
     * @param OutputChannelInterface $outputChannel
     * @param ArticleInterface       $article
     */
    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void;

    /**
     * @param OutputChannelInterface $outputChannel
     * @param ArticleInterface       $article
     */
    public function publish(OutputChannelInterface $outputChannel, ArticleInterface $article): void;

    /**
     * @param OutputChannelInterface $outputChannel
     * @param ArticleInterface       $article
     */
    public function unpublish(OutputChannelInterface $outputChannel, ArticleInterface $article): void;

    /**
     * @param OutputChannelInterface $outputChannel
     *
     * @return bool
     */
    public function supports(OutputChannelInterface $outputChannel): bool;
}
