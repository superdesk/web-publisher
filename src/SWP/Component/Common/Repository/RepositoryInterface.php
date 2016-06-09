<?php

namespace SWP\Component\Common\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use SWP\Component\Common\Model\PersistableInterface;

interface RepositoryInterface extends ObjectRepository
{
    public function add(PersistableInterface $object);

    public function remove(PersistableInterface $object);
}
