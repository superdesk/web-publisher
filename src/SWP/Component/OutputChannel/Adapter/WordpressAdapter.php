<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Output Channel Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\OutputChannel\Adapter;

use GuzzleHttp\ClientInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;

final class WordpressAdapter implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $config = [];

    /**
     * WordpressAdapter constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function send(ArticleInterface $article): void
    {
        $url = $this->config['url'];

        $this->client->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $article->getBody(),
            'timeout' => 5,
        ]);
    }
}
