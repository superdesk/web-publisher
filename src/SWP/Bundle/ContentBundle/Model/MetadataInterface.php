<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Storage\Model\PersistableInterface;

interface MetadataInterface extends PersistableInterface
{
    public function getSubjects(): Collection;

    public function addSubject(SubjectInterface $subject): void;

    public function removeSubject(SubjectInterface $subject): void;

    public function hasSubject(SubjectInterface $subject): bool;

    public function getServices(): Collection;

    public function addService(ServiceInterface $service): void;

    public function removeService(ServiceInterface $service): void;

    public function hasService(ServiceInterface $service): bool;

    public function getProfile(): ?string;

    public function setProfile(?string $profile): void;
}
