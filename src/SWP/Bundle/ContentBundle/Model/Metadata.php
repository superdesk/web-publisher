<?php

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

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Metadata implements MetadataInterface
{
    public const SERVICE_KEY = 'service';

    public const SUBJECT_KEY = 'subject';

    public const PLACE_KEY = 'place';

    /** @var int */
    protected $id;

    /** @var Collection|SubjectInterface[] */
    protected $subjects;

    /** @var Collection|ServiceInterface[] */
    protected $services;

    /** @var Collection|PlaceInterface[] */
    protected $places;

    /** @var string|null */
    protected $profile;

    /** @var string|null */
    protected $guid;

    /** @var string|null */
    protected $urgency;

    /** @var string|null */
    protected $priority;

    /** @var string|null */
    protected $located;

    /** @var string|null */
    protected $byline;

    /** @var string|null */
    protected $language;

    /** @var string|null */
    protected $edNote;

    /** @var string|null */
    protected $genre;

    /** @var ArticleInterface */
    protected $article;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->places = new ArrayCollection();
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

    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(PlaceInterface $place): void
    {
        if (!$this->hasPlace($place)) {
            $place->setMetadata($this);
            $this->places->add($place);
        }
    }

    public function removePlace(PlaceInterface $place): void
    {
        if ($this->hasPlace($place)) {
            $place->setMetadata(null);
            $this->places->removeElement($place);
        }
    }

    public function hasPlace(PlaceInterface $place): bool
    {
        return $this->places->contains($place);
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): void
    {
        $this->profile = $profile;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): void
    {
        $this->guid = $guid;
    }

    public function getUrgency(): ?int
    {
        return $this->urgency;
    }

    public function setUrgency(?int $urgency): void
    {
        $this->urgency = $urgency;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    public function getLocated(): ?string
    {
        return $this->located;
    }

    public function setLocated(?string $located): void
    {
        $this->located = $located;
    }

    public function getByline(): ?string
    {
        return $this->byline;
    }

    public function setByline(?string $byline): void
    {
        $this->byline = $byline;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getEdNote(): ?string
    {
        return $this->edNote;
    }

    public function setEdNote(?string $edNote): void
    {
        $this->edNote = $edNote;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): void
    {
        $this->genre = $genre;
    }

    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
    }
}
