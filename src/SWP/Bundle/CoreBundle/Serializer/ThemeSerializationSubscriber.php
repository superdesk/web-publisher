<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SWP\Bundle\CoreBundle\Theme\Model\Theme;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ThemeSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'class' => Theme::class,
                'method' => 'onPostSerialize',
            ],
        ];
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $data = $event->getObject();

        if (0 !== count($screenshots = $data->getScreenshots())) {
            $urlAwareScreenshots = [];
            foreach ($screenshots as $screenshot) {
                $themeName = $data->getName();
                if (false !== ($pos = strpos($themeName, '@'))) {
                    $themeName = $themeName = substr($themeName, 0, $pos);
                }
                $themeName = str_replace('/', '__', $themeName);
                $url = $this->router->generate(
                    'static_theme_screenshots',
                    [
                        'type' => 'organization',
                        'themeName' => $themeName,
                        'fileName' => str_replace('screenshots/', '', $screenshot->getPath()),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $urlAwareScreenshot = [];
                $urlAwareScreenshot['url'] = $url;
                $urlAwareScreenshot['path'] = $screenshot->getPath();
                $urlAwareScreenshot['title'] = $screenshot->getTitle();
                $urlAwareScreenshot['description'] = $screenshot->getDescription();
                $urlAwareScreenshots[] = $urlAwareScreenshot;
            }
            $event->getVisitor()->setData('screenshots', $urlAwareScreenshots);
        }
    }
}
