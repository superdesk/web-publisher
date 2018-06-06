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

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ExternalArticle;
use SWP\Bundle\CoreBundle\Model\ExternalArticleInterface;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;
use SWP\Bundle\CoreBundle\OutputChannel\External\Wordpress\Post;
use SWP\Bundle\CoreBundle\OutputChannel\External\Wordpress\PostInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class WordpressAdapter implements AdapterInterface
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'publish';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RepositoryInterface
     */
    private $externalArticleRepository;

    /**
     * @var EntityManagerInterface
     */
    private $externalArticleManager;

    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    /**
     * WordpressAdapter constructor.
     *
     * @param ClientInterface        $client
     * @param RepositoryInterface    $externalArticleRepository
     * @param EntityManagerInterface $externalArticleManager
     * @param MediaManagerInterface  $mediaManager
     */
    public function __construct(
        ClientInterface $client,
        RepositoryInterface $externalArticleRepository,
        EntityManagerInterface $externalArticleManager,
        MediaManagerInterface $mediaManager
    ) {
        $this->client = $client;
        $this->externalArticleRepository = $externalArticleRepository;
        $this->externalArticleManager = $externalArticleManager;
        $this->mediaManager = $mediaManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $post = $this->createPost($outputChannel, $article);
        $post->setStatus(self::STATUS_DRAFT);
        $response = $this->send($outputChannel, 'posts', $post);
        if (201 === $response->getStatusCode()) {
            $responseData = \json_decode($response->getBody()->getContents(), true);
            $externalArticle = new ExternalArticle($article, (string) $responseData['id'], 'draft');
            if (isset($responseData['link'])) {
                $externalArticle->setLiveUrl($responseData['link']);
            }
            if (null !== $responseData['featured_media']) {
                $externalArticle->setExtra(['featured_media' => $responseData['featured_media']]);
            }
            $article->setExternalArticle($externalArticle);
            $this->externalArticleRepository->add($externalArticle);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $externalArticle = $this->getExternalArticle($article);
        $this->handleArticleUpdate($outputChannel, $article, $externalArticle->getStatus());
    }

    /**
     * {@inheritdoc}
     */
    public function publish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $this->handleArticleUpdate($outputChannel, $article, self::STATUS_PUBLISHED);
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $this->handleArticleUpdate($outputChannel, $article, self::STATUS_DRAFT);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OutputChannelInterface $outputChannel): bool
    {
        return OutputChannelInterface::TYPE_WORDPRESS === $outputChannel->getType();
    }

    /**
     * @param OutputChannelInterface $outputChannel
     * @param ArticleInterface       $article
     * @param string                 $status
     */
    private function handleArticleUpdate(OutputChannelInterface $outputChannel, ArticleInterface $article, string $status): void
    {
        $post = $this->createPost($outputChannel, $article);
        $post->setStatus($status);
        $externalArticle = $this->getExternalArticle($article);
        $response = $this->send($outputChannel, sprintf('posts/%s', $externalArticle->getExternalId()), $post);

        if (self::STATUS_PUBLISHED === $status && null === $externalArticle->getPublishedAt()) {
            $externalArticle->setPublishedAt(new \DateTime());
        } elseif (self::STATUS_DRAFT === $status && $externalArticle->getPublishedAt() instanceof \DateTime) {
            $externalArticle->setUnpublishedAt(new \DateTime());
        }

        $this->handleExternalArticleUpdate($article, $externalArticle, $response);
    }

    /**
     * @param ArticleInterface $article
     *
     * @return ExternalArticleInterface
     */
    private function getExternalArticle(ArticleInterface $article): ExternalArticleInterface
    {
        $externalArticle = $article->getExternalArticle();
        if (null === $externalArticle) {
            throw new \BadMethodCallException('You try to work on not existing external article');
        }

        return $externalArticle;
    }

    /**
     * @param ArticleInterface         $article
     * @param ExternalArticleInterface $externalArticle
     * @param GuzzleResponse           $response
     */
    private function handleExternalArticleUpdate(ArticleInterface $article, ExternalArticleInterface $externalArticle, GuzzleResponse $response): void
    {
        if (200 === $response->getStatusCode()) {
            $responseData = \json_decode($response->getBody()->getContents(), true);
            $externalArticle->setStatus($responseData['status']);
            if (isset($responseData['link'])) {
                $externalArticle->setLiveUrl($responseData['link']);
            }
            if (null !== $responseData['featured_media']) {
                $externalArticle->setExtra(['featured_media' => $responseData['featured_media']]);
            }
            $externalArticle->setUpdatedAt(new \DateTime());
            $article->setExternalArticle($externalArticle);
            $this->externalArticleManager->flush();
        }
    }

    /**
     * @param OutputChannelInterface $outputChannel
     * @param ArticleInterface       $article
     *
     * @return Post
     */
    private function createPost(OutputChannelInterface $outputChannel, ArticleInterface $article): Post
    {
        $post = new Post();
        $post->setTitle($article->getTitle());
        $post->setContent($article->getBody());
        $post->setSlug($article->getSlug());
        $post->setType(PostInterface::TYPE_STANDARD);

        if (null !== $featureMedia = $article->getFeatureMedia()) {
            $image = $featureMedia->getImage();
            $edge = 'media';
            $externalArticle = $article->getExternalArticle();
            if (null !== $externalArticle) {
                if (null !== $featuredMediaId = $externalArticle->getExtra()['featured_media']) {
                    $edge .= '/'.$featuredMediaId;
                }
            }

            try {
                $response = $this->send(
                    $outputChannel,
                    $edge,
                    new Post(),
                    [
                        'headers' => [
                            'Content-Type' => $featureMedia->getMimetype(),
                            'Content-Disposition' => 'attachment; filename="'.$image->getAssetId().'.'.$image->getFileExtension().'"',
                        ],
                        'body' => $this->mediaManager->getFile($article->getFeatureMedia()->getImage()),
                        'timeout' => 5,
                    ]
                );
                $decodedBody = \json_decode($response->getBody()->getContents(), true);
                $post->setFeaturedMedia($decodedBody['id']);
            } catch (RequestException $e) {
                // ignore feature media
            }
        }

        $post->setTags($article->getKeywords());

        return $post;
    }

    /**
     * @param OutputChannelInterface $outputChannel
     * @param string                 $endpoint
     * @param Post                   $post
     * @param array|null             $requestOptions
     *
     * @return GuzzleResponse
     */
    private function send(OutputChannelInterface $outputChannel, string $endpoint, Post $post, array $requestOptions = null): GuzzleResponse
    {
        $url = $outputChannel->getConfig()['url'];
        $authorizationKey = $outputChannel->getConfig()['authorization_key'];

        if (null === $requestOptions) {
            $requestOptions = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $this->getSerializer()->serialize($post, 'json'),
                'timeout' => 5,
            ];
        }

        if (isset($requestOptions['headers'])) {
            $requestOptions['headers']['Authorization'] = $authorizationKey;
        }
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $this->client->post($url.'/wp-json/wp/v2/'.$endpoint, $requestOptions);

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
