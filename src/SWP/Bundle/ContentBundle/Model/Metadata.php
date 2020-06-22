<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Metadata implements MetadataInterface
{
    /** @var int */
    protected $id;

    /** @var Collection|SubjectInterface[] */
    protected $subjects;

    /** @var Collection|ServiceInterface[] */
    protected $services;

    /** @var string|null */
    protected $profile;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(SubjectInterface $subject): void
    {
        if (!$this->hasSubject($subject)) {
            $subject->setMetadata($this);
            $this->subjects->add($subject);
        }
    }

    public function removeSubject(SubjectInterface $subject): void
    {
        if ($this->hasSubject($subject)) {
            $subject->setMetadata(null);
            $this->subjects->removeElement($subject);
        }
    }

    public function hasSubject(SubjectInterface $subject): bool
    {
        return $this->subjects->contains($subject);
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(ServiceInterface $service): void
    {
        if (!$this->hasService($service)) {
            $service->setMetadata($this);
            $this->services->add($service);
        }
    }

    public function removeService(ServiceInterface $service): void
    {
        if ($this->hasService($service)) {
            $service->setMetadata(null);
            $this->services->removeElement($service);
        }
    }

    public function hasService(ServiceInterface $service): bool
    {
        return $this->services->contains($service);
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): void
    {
        $this->profile = $profile;
    }
}
