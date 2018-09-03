<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\BridgeBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\PackageInterface;

class PackageSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
            ],
        ];
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostDeserialize(ObjectEvent $event)
    {
        if ($event->getObject() instanceof PackageInterface) {
            /** @var PackageInterface $package */
            $package = $event->getObject();

            foreach ($package->getItems() as $item) {
                $this->processRenditions($item);

                foreach ($item->getItems() as $imageItem) {
                    $this->processRenditions($imageItem);
                }
            }

            $this->processGroups($package);

            foreach ($package->getItems() as $key => $item) {
                $item->setName($key);
            }
        }
    }

    private function processGroups(PackageInterface $package): void
    {
        foreach ((array) $package->getGroups() as $groups) {
            foreach ((array) $groups as $group) {
                foreach ($group->getItems() as $groupItem) {
                    $groupItem->setGroup($group);
                    $groupItem->setName($groupItem->getGuid());
                    $this->processRenditions($groupItem);
                }

                $group->setPackage($package);
            }
        }
    }

    private function processRenditions(ItemInterface $item)
    {
        foreach ($item->getRenditions() as $key => $rendition) {
            $rendition->setName($key);
            $rendition->setItem($item);
        }
    }
}
