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

class RevisionService
{
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getUnpublishedRevision($parentId, $className)
    {
        $repository = $this->objectManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Revision');
        $revision = $repository
            ->findOneBy(['revisionId' => $parentId, 'className' => $className])
            ->getOneOrNullResult();

        $originId = $parentId;
        if ($revision) {
            if (!$revision->getPublished()) {
                return $revision;
            }
            else {
                $originId = $revision->getOriginId();
            }
        }

        $unpublishedRevision = $repository
            ->findOneBy(['originId' => $originId, 'className' => $className, 'published' => false])
            ->getOneOrNullResult();

        return $unpublishedRevision;
    }

    public function getWorkingVersion($parentId, $className)
    {
        $repository = $this->objectManager->getRepository($className);
        $parent = $repository->find($parentId);

        $unpublishedRevision = $this->getUnpublishedRevision($parentId, $className);

        // Get existing working copy
        if (null !== $unpublishedRevision) {
            $revisionId = $unpublishedRevision->getRevisionId();
            if ($revisionId === $parentId) {
                return $parent;
            }

            $workingVersion = $repository
                ->getById($unpublishedRevision->getRevisionId())
                ->getOneOrNullResult();
            return $workingVersion;
        }

        // Create working copy
        $workingVersion = clone $parent;
        $name = $parent->getName().'_'.uniqid();
        $workingVersion->setName($name);
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($workingVersion);
        $em->flush();

        $nextRevision = new Revision();
        $originId = null === $unpublishedRevision ? $parent->getId() : $unpublishedRevision->getOriginId();
        $nextRevision->setOriginId($originId);
        $nextRevision->setRevisionId($workingVersion->getId());
        $nextRevision->setClassName($className);
        $em->persist($nextRevision);
        $em->flush();
        return $workingVersion;
    }

    public function publishWorkingVersion($parentId, $className)
    {
        $repository = $this->objectManager->getRepository($className);
        $parent = $repository->find($parentId);
        $revision = $this->getUnpublishedRevision($parentId);
        if (null === $revision || $parentId === $revision->getRevisionId()) {
            throw new \Exception("No unpublished revision found");
        }

        $workingVersion = $repository->find($revision->getRevisionId());

        $workingVersion->setName($parent->getName());
        $parent->setName($parent->getName().'_'.uniqid());
        $revision->setPublished(true);

        $this->objectManager->flush();
        return $workingVersion;
    }

    public function getPublishedRevisions($className)
    {

    }
}
