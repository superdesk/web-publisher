<?php

namespace SWP\Bundle\ContentBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use SWP\Component\Common\Model\PersistableInterface;
use SWP\Component\Common\Repository\RepositoryInterface;

class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(PersistableInterface $object)
    {
        $this->dm->persist($object);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(PersistableInterface $object)
    {
        if (null !== $this->find($object->getId())) {
            $this->dm->remove($object);
            $this->dm->flush();
        }
    }
}
