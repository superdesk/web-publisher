<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\ArrayCollection;

class BaseContent implements ContentInterface
{
    use AuthorsAwareTrait;

    private const PHOTO_LICENSE = 'photo_license';

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $guid;

    /**
     * @var string
     */
    protected $headline;

    /**
     * @var string
     */
    protected $byline;

    /**
     * @var string
     */
    protected $slugline;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var array
     */
    protected $subjects = [];

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $places = [];

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var string
     */
    protected $located;

    /**
     * @var int
     */
    protected $urgency = 0;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var int
     */
    protected $version;

    /**
     * @var string
     */
    protected $genre;

    /**
     * @var string
     */
    protected $edNote;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $keywords = [];

    /**
     * @var string
     */
    protected $pubStatus = ContentInterface::STATUS_USABLE;

    /**
     * @var string|null
     */
    protected $evolvedFrom;

    /**
     * @var string|null
     */
    protected $source;

    /**
     * @var array
     */
    protected $extra = [];

    /**
     * @var \DateTimeInterface|null
     */
    protected $firstPublishedAt;

    /**
     * @var string|null
     */
    protected $copyrightNotice;

    /**
     * @var string|null
     */
    protected $copyrightHolder;

    /** @var string|null */
    protected $profile;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getByline()
    {
        return $this->byline;
    }

    /**
     * {@inheritdoc}
     */
    public function setByline($byline)
    {
        $this->byline = $byline;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugline()
    {
        return $this->slugline;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlugline($slugline)
    {
        $this->slugline = $slugline;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * {@inheritdoc}
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjects(array $subjects = [])
    {
        $this->subjects = $subjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlaces($places)
    {
        $this->places = $places;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocated()
    {
        return $this->located;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocated($located)
    {
        $this->located = $located;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * {@inheritdoc}
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function getServicesNames(): array
    {
        return $this->mapNames($this->services);
    }

    /**
     * {@inheritdoc}
     */
    public function getServicesCodes(): array
    {
        return $this->mapCodes($this->services);
    }

    public function getSubjectsSchemes(): array
    {
        return $this->mapSchemes($this->subjects);
    }

    public function getSubjectsNames(): array
    {
        return $this->mapNames($this->subjects);
    }

    private function mapNames(array $values): array
    {
        return array_map(function ($subject) {
            if (\is_array($subject) && \array_key_exists('name', $subject)) {
                return $subject['name'];
            }

            return $subject;
        }, $values);
    }

    private function mapCodes(array $values): array
    {
        return array_map(function ($subject) {
            if (\is_array($subject) && \array_key_exists('code', $subject)) {
                return $subject['code'];
            }

            return $subject;
        }, $values);
    }

    private function mapSchemes(array $values): array
    {
        return array_map(function ($subject) {
            if (\is_array($subject) && \array_key_exists('scheme', $subject)) {
                return $subject['scheme'];
            }

            return $subject;
        }, $values);
    }

    public function getLicense(): array
    {
        foreach ($this->subjects as $subject) {
            if (\is_array($subject) && \array_key_exists('scheme', $subject) && self::PHOTO_LICENSE === $subject['scheme']) {
                return $subject;
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setServices(array $services = [])
    {
        $this->services = $services;
    }

    /**
     * {@inheritdoc}
     */
    public function getEdNote()
    {
        return $this->edNote;
    }

    /**
     * {@inheritdoc}
     */
    public function setEdNote($edNote)
    {
        $this->edNote = $edNote;
    }

    /**
     * {@inheritdoc}
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * {@inheritdoc}
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'subject' => $this->getSubjects(),
            'urgency' => $this->getUrgency(),
            'priority' => $this->getPriority(),
            'located' => $this->getLocated(),
            'place' => $this->getPlaces(),
            'service' => $this->getServices(),
            'type' => $this->getType(),
            'byline' => $this->getByline(),
            'guid' => $this->getGuid(),
            'edNote' => $this->getEdNote(),
            'genre' => $this->getGenre(),
            'language' => $this->getLanguage(),
            'profile' => $this->getProfile(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setPubStatus(string $pubStatus)
    {
        $this->pubStatus = $pubStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvolvedFrom()
    {
        return $this->evolvedFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function setEvolvedFrom(string $evolvedFrom)
    {
        $this->evolvedFrom = $evolvedFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtra(): array
    {
        if (null === $this->extra) {
            return [];
        }

        return $this->extra;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtra(?array $extra): void
    {
        $this->extra = $extra;
    }

    public function getFirstPublishedAt(): ?\DateTimeInterface
    {
        return $this->firstPublishedAt;
    }

    public function setFirstPublishedAt(?\DateTimeInterface $firstPublishedAt): void
    {
        $this->firstPublishedAt = $firstPublishedAt;
    }

    public function getCopyrightNotice(): ?string
    {
        return $this->copyrightNotice;
    }

    public function setCopyrightNotice(?string $copyrightNotice): void
    {
        $this->copyrightNotice = $copyrightNotice;
    }

    public function getCopyrightHolder(): ?string
    {
        return $this->copyrightHolder;
    }

    public function setCopyrightHolder(?string $copyrightHolder): void
    {
        $this->copyrightHolder = $copyrightHolder;
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
