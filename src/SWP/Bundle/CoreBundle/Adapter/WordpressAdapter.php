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

use GuzzleHttp\ClientInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;
use SWP\Bundle\CoreBundle\OutputChannel\External\Wordpress\Post;
use SWP\Component\Common\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;

final class WordpressAdapter implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * WordpressAdapter constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->serializer = new Serializer();
    }

    /**
     * {@inheritdoc}
     */
    public function create(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $post = $this->createPost($article);
        $this->send($outputChannel, $post);
    }

    /**
     * {@inheritdoc}
     */
    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function publish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OutputChannelInterface $outputChannel): bool
    {
        return OutputChannelInterface::TYPE_WORDPRESS === $outputChannel->getType();
    }

    /**
     * @param ArticleInterface $article
     *
     * @return Post
     */
    private function createPost(ArticleInterface $article): Post
    {
        $post = new Post();
        $post->setTitle($article->getTitle());
        $post->setContent($article->getBody());
        $post->setSlug($article->getSlug());
        $post->setStatus('draft');

        return $post;
    }

    private function updatePost(ArticleInterface $article)
    {
    }

    /**
     * @param OutputChannelInterface $outputChannel
     * @param Post                   $post
     */
    private function send(OutputChannelInterface $outputChannel, Post $post): void
    {
        $url = $outputChannel->getConfig()['url'];

        $this->client->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $this->serializer->serialize($post, 'json'),
            'timeout' => 5,
        ]);
    }
}
