<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\TemplateEngineBundle\Model\Revision;

class RevisionService
{
    const REVISION_NAME_SEPARATOR = '_';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getUnpublishedRevision($published)
    {
        $repository = $this->objectManager->getRepository(get_class($published));
        $originId = $this->getOriginId($published);
        $published = $repository
            ->findOneBy(['originId' => $originId, 'state' => Revision::STATE_UNPUBLISHED]);

        return $published;
    }

    public function createUnpublishedRevision($published)
    {
        if (Revision::STATE_PUBLISHED !== $published->getState()) {
            throw new \Exception('Cannot create unpublished version of version which is not published');
        }

        // Create working copy
        $unpublished = $published->createNextRevision();

        $name = $published->getName().self::REVISION_NAME_SEPARATOR.uniqid();
        $unpublished->setName($name);
        $originId = $this->getOriginId($published);
        $unpublished->setOriginId($originId);
        $unpublished->setState(Revision::STATE_UNPUBLISHED);

        $this->objectManager->persist($unpublished);
        $this->objectManager->flush();

        return $unpublished;
    }

    public function getOrCreateUnpublishedRevision($revision)
    {
        if (Revision::STATE_UNPUBLISHED === $revision->getState()) {
            return $revision;
        }

        $unpublished = $this->getUnpublishedRevision($revision);
        if (null === $unpublished) {
            $unpublished = $this->createUnpublishedRevision($revision);
        }

        return $unpublished;
    }

    public function isNameUnchanged($published, $unpublished)
    {
        $name = $published->getName();
        $unpublishedName = $unpublished->getName();

        // Assume name is not change if has the same name as the published version, plus the separator and a uniqid
        return strpos($unpublishedName, $name) === 0
            && strlen($unpublishedName) === strlen($name) + strlen(self::REVISION_NAME_SEPARATOR) + 13;
    }

    public function publishUnpublishedRevision($published)
    {
        if (Revision::STATE_PUBLISHED !== $published->getState()) {
            throw new \Exception('Published version should be in published state', 409);
        }

        $unpublished = $this->getUnpublishedRevision($published);
        if (null === $unpublished) {
            throw new \Exception('No unpublished version', 410);
        }

        $unpublished->setState(Revision::STATE_PUBLISHED);
        if ($this->isNameUnchanged($published, $unpublished)) {
            $unpublished->setName($published->getName());
        }

        $published->setState(Revision::STATE_ARCHIVED);
        $published->setName($published->getName().self::REVISION_NAME_SEPARATOR.uniqid());

        $unpublished->onPublished($published);

        $this->objectManager->flush();

        return $unpublished;
    }

    public function getCurrentRevisions($className)
    {
        $repository = $this->objectManager->getRepository($className);

        return $repository->findBy(array('state' => Revision::STATE_PUBLISHED));
    }

    public function getAllRevisions($published)
    {
        $repository = $this->objectManager->getRepository(get_class($published));

        return $repository->findBy(array('state' => array(Revision::STATE_ARCHIVED, Revision::STATE_PUBLISHED)), array('createdAt' => 'DESC'));
    }

    private function getOriginId($object)
    {
        return null === $object->getOriginId() ? $object->getId() : $object->getOriginId();
    }
}
