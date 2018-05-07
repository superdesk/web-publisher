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
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ExternalArticle;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;
use SWP\Bundle\CoreBundle\OutputChannel\External\Wordpress\Post;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class WordpressAdapter implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RepositoryInterface
     */
    private $externalArticleRepository;

    /**
     * WordpressAdapter constructor.
     *
     * @param ClientInterface     $client
     * @param RepositoryInterface $externalArticleRepository
     */
    public function __construct(ClientInterface $client, RepositoryInterface $externalArticleRepository)
    {
        $this->client = $client;
        $this->externalArticleRepository = $externalArticleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $post = $this->createPost($article);
        $post->setStatus('draft');
        $response = $this->send($outputChannel, '/posts', $post);

        if (201 === $response->getStatusCode()) {
            $responseData = \json_decode($response->getBody()->getContents(), true);
            $externalArticle = new ExternalArticle($article, (string) $responseData['id'], 'draft');
            if (isset($responseData['link'])) {
                $externalArticle->setLiveUrl($responseData['link']);
            }
            $this->externalArticleRepository->add($externalArticle);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $post = $this->createPost($article);
        $externalArticle = $article->getExternalArticle();
        if (null === $externalArticle) {
            throw new \BadMethodCallException('You try to update not created external article');
        }

        $post->setStatus($externalArticle->getStatus());
        $response = $this->send($outputChannel, sprintf('/posts/%s', $externalArticle->getExternalId()), $post);
        if (200 === $response->getStatusCode()) {
            $responseData = \json_decode($response->getBody()->getContents(), true);
            if (isset($responseData['link'])) {
                $externalArticle->setLiveUrl($responseData['link']);
            }
            $externalArticle->setUpdatedAt(new \DateTime());
            $this->externalArticleRepository->flush();
        }
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

        return $post;
    }

    /**
     * @param OutputChannelInterface $outputChannel
     * @param string                 $endpoint
     * @param Post                   $post
     *
     * @return GuzzleResponse
     */
    private function send(OutputChannelInterface $outputChannel, string $endpoint, Post $post): GuzzleResponse
    {
        $url = $outputChannel->getConfig()['url'];
        $authorizationKey = $outputChannel->getConfig()['authorization_key'];

        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $this->client->post($url.'/wp-json/wp/v2/'.$endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $authorizationKey,
            ],
            'body' => $this->getSerializer()->serialize($post, 'json'),
            'timeout' => 5,
        ]);

        return $response;
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer(): SerializerInterface
    {
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizers = [$normalizer];

        return new Serializer($normalizers, $encoders);
    }
}
