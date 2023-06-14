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
 * @copyright 2018 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class AuthorSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'class' => ArticleAuthor::class,
                'method' => 'onPostSerialize',
            ],
        ];
    }

    public function onPostSerialize(ObjectEvent $event): void
    {
        $data = $event->getObject();

        if (null !== ($mediaId = $data->getAvatarUrl())) {
            $url = $this->router->generate('swp_author_media_get', [
                'mediaId' => pathinfo($mediaId, PATHINFO_FILENAME),
                'extension' => pathinfo($mediaId, PATHINFO_EXTENSION),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $event->getVisitor()->visitProperty(
                new StaticPropertyMetadata('', 'avatar_url', $url),
                $url
            );
        }
    }
}
