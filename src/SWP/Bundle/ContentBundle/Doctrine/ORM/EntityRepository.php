<?php

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use SWP\Component\Common\Model\PersistableInterface;
use SWP\Component\Common\Repository\RepositoryInterface;

class EntityRepository extends BaseEntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(PersistableInterface $object)
    {
        $this->_em->persist($object);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(PersistableInterface $object)
    {
        if (null !== $this->find($object->getId())) {
            $this->_em->remove($object);
            $this->_em->flush();
        }
    }
}
