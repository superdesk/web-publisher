<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Model\MetadataInterface;
use SWP\Bundle\ContentBundle\Model\Place;
use SWP\Bundle\ContentBundle\Model\Service;
use SWP\Bundle\ContentBundle\Model\Subject;

final class MetadataFactory implements MetadataFactoryInterface
{
    /** @var string */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function create(): MetadataInterface
    {
        return new $this->className();
    }

    public function createFrom(array $legacyMetadata): MetadataInterface
    {
        $metadata = $this->create();
        if (isset($legacyMetadata['subject'])) {
            foreach ($legacyMetadata['subject'] as $legacySubject) {
                $subject = new Subject();
                $subject->setCode($legacySubject['code']);
                $subject->setScheme($legacySubject['scheme'] ?? null);

                $metadata->addSubject($subject);
            }
        }

        if (isset($legacyMetadata['service'])) {
            foreach ($legacyMetadata['service'] as $legacyService) {
                $service = new Service();
                $service->setCode($legacyService['code'] ?? $legacyService['name'] ?? null);

                $metadata->addService($service);
            }
        }

        if (isset($legacyMetadata['place'])) {
            foreach ($legacyMetadata['place'] as $legacyPlace) {
                $place = new Place();
                $place->setCountry($legacyPlace['country'] ?? null);
                $place->setGroup($legacyPlace['group'] ?? null);
                $place->setName($legacyPlace['name'] ?? null);
                $place->setState($legacyPlace['state'] ?? null);
                $place->setQcode($legacyPlace['qcode'] ?? null);
                $place->setWorldRegion($legacyPlace['world_region'] ?? null);

                $metadata->addPlace($place);
            }
        }

        $metadata->setProfile($legacyMetadata['profile'] ?? null);
        $metadata->setUrgency($legacyMetadata['urgency'] ?? null);
        $metadata->setPriority($legacyMetadata['priority'] ?? null);
        $metadata->setEdNote($legacyMetadata['edNote'] ?? null);
        $metadata->setLanguage($legacyMetadata['language'] ?? null);
        $metadata->setGuid($legacyMetadata['guid'] ?? null);
        $metadata->setGenre(isset($legacyMetadata['genre']) ? $legacyMetadata['genre'][0]['code'] : null);
        $metadata->setLocated($legacyMetadata['located'] ?? null);
        $metadata->setByline($legacyMetadata['byline'] ?? null);

        return $metadata;
    }
}
