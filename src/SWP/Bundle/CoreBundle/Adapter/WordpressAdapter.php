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
        $this->handleArticleCreate($outputChannel, $article);
    }

    /**
     * {@inheritdoc}
     */
    public function update(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $externalArticle = $article->getExternalArticle();
        if (null === $externalArticle) {
            return;
        }

        $this->handleArticleUpdate($outputChannel, $article, $externalArticle->getStatus());
    }

    /**
     * {@inheritdoc}
     */
    public function publish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $externalArticle = $article->getExternalArticle();
        if (null === $externalArticle) {
            return;
        }

        $this->handleArticleUpdate($outputChannel, $article, self::STATUS_PUBLISHED);
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $externalArticle = $article->getExternalArticle();
        if (null === $externalArticle) {
            return;
        }

        $this->handleArticleUpdate($outputChannel, $article, self::STATUS_DRAFT);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OutputChannelInterface $outputChannel): bool
    {
        return OutputChannelInterface::TYPE_WORDPRESS === $outputChannel->getType();
    }

    private function handleArticleCreate(OutputChannelInterface $outputChannel, ArticleInterface $article): void
    {
        $post = $this->createPost($outputChannel, $article);
        $post->setStatus(self::STATUS_DRAFT);
        $response = $this->send($outputChannel, 'posts', $post);

        if (201 !== $response->getStatusCode()) {
            // wrong answer from wordpress - skip it
            return;
        }

        $this->handleExternalArticleUpdateOrCreate($article, $response, $article->getExternalArticle());
    }

    private function handleArticleUpdate(OutputChannelInterface $outputChannel, ArticleInterface $article, string $status): void
    {
        $post = $this->createPost($outputChannel, $article);
        $post->setStatus($status);
        $externalArticle = $article->getExternalArticle();
        $response = $this->send($outputChannel, sprintf('posts/%s', $externalArticle->getExternalId()), $post);

        if (self::STATUS_PUBLISHED === $status && null === $externalArticle->getPublishedAt()) {
            $externalArticle->setPublishedAt(new \DateTime());
        } elseif (self::STATUS_DRAFT === $status && $externalArticle->getPublishedAt() instanceof \DateTime) {
            $externalArticle->setUnpublishedAt(new \DateTime());
        }

        if (200 !== $response->getStatusCode()) {
            // wrong answer from wordpress - skip it
            return;
        }

        $this->handleExternalArticleUpdateOrCreate($article, $response, $externalArticle);
    }

    private function handleExternalArticleUpdateOrCreate(ArticleInterface $article, GuzzleResponse $response, ExternalArticleInterface $externalArticle = null): ExternalArticleInterface
    {
        $responseData = \json_decode($response->getBody()->getContents(), true);
        if (null === $externalArticle) {
            $externalArticle = new ExternalArticle($article, (string) $responseData['id'], 'draft');
        }
        $article->setExternalArticle($externalArticle);
        if (isset($responseData['link'])) {
            $externalArticle->setLiveUrl($responseData['link']);
        }
        if (null !== $responseData['featured_media']) {
            $externalArticle->setExtra(['featured_media' => $responseData['featured_media']]);
        }

        if (200 === $response->getStatusCode()) {
            $externalArticle->setStatus($responseData['status']);
            $externalArticle->setUpdatedAt(new \DateTime());
            $this->externalArticleManager->flush();
        } elseif (201 === $response->getStatusCode()) {
            $this->externalArticleRepository->add($externalArticle);
        }

        return $externalArticle;
    }

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
                if (is_array($decodedBody)) {
                    $post->setFeaturedMedia($decodedBody['id']);
                }
            } catch (RequestException $e) {
                // ignore feature media
            }
        }

        return $post;
    }

    private function send(OutputChannelInterface $outputChannel, string $endpoint, Post $post, array $requestOptions = null): GuzzleResponse
    {
        $url = $outputChannel->getConfig()['url'];
        $authorizationKey = $outputChannel->getConfig()['authorizationKey'];

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

        try {
            /** @var \GuzzleHttp\Psr7\Response $response */
            $response = $this->client->post($url.'/wp-json/wp/v2/'.$endpoint, $requestOptions);
        } catch (RequestException $e) {
            return new GuzzleResponse(500);
        }

        return $response;
    }

    private function getSerializer(): SerializerInterface
    {
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizers = [$normalizer];

        return new Serializer($normalizers, $encoders);
    }
}
