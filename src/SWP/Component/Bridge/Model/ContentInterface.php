<?php

declare(strict_types=1);

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

use SWP\Component\Storage\Model\PersistableInterface;

interface ContentInterface extends PersistableInterface, AuthorsAwareInterface
{
    const STATUS_USABLE = 'usable';

    const STATUS_CANCELED = 'canceled';

    const STATUS_PUBLISHED = 'published';

    const STATUS_UNPUBLISHED = 'unpublished';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getGuid();

    /**
     * @param string $guid
     */
    public function setGuid($guid);

    /**
     * @return string
     */
    public function getHeadline();

    /**
     * @param string $headline
     */
    public function setHeadline($headline);

    /**
     * @return string
     */
    public function getByLine();

    /**
     * @param string $byline
     */
    public function setByLine($byline);

    /**
     * @return string
     */
    public function getSlugline();

    /**
     * @param string $slugline
     */
    public function setSlugline($slugline);

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @param string $language
     */
    public function setLanguage($language);

    /**
     * @return array
     */
    public function getSubjects();

    public function setSubjects(array $subjects = []);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getPlaces();

    /**
     * @param array $places
     */
    public function setPlaces($places);

    /**
     * @return string
     */
    public function getLocated();

    /**
     * @param string $located
     */
    public function setLocated($located);

    /**
     * @return int
     */
    public function getUrgency();

    /**
     * @param int $urgency
     */
    public function setUrgency($urgency);

    /**
     * @return int
     */
    public function getVersion();

    /**
     * @param int $version
     */
    public function setVersion($version);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return array
     */
    public function getServices();

    public function getServicesNames(): array;

    public function getServicesCodes(): array;

    public function setServices(array $services = []);

    /**
     * @return string
     */
    public function getEdNote();

    /**
     * @param string $edNote
     */
    public function setEdNote($edNote);

    /**
     * @return string
     */
    public function getGenre();

    /**
     * @param string $genre
     */
    public function setGenre($genre);

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return array
     */
    public function getKeywords();

    public function setKeywords(array $keywords);

    /**
     * @return string
     */
    public function getPubStatus();

    public function setPubStatus(string $pubStatus);

    /**
     * @return string|null
     */
    public function getEvolvedFrom();

    public function setEvolvedFrom(string $evolvedFrom);

    /**
     * @return string|null
     */
    public function getSource();

    /**
     * @param string|null $source
     */
    public function setSource($source);

    public function getExtra(): ?array;

    public function setExtra(?array $extra): void;

    public function getFirstPublishedAt(): ?\DateTimeInterface;

    public function setFirstPublishedAt(?\DateTimeInterface $firstPublishedAt): void;

    public function getSubjectsSchemes(): array;

    public function getSubjectsNames(): array;

    public function getBody(): ?string;

    public function setBody(string $body): void;

    public function getBodyText(): ?string;

    public function setBodyText(string $bodyText): void;

    public function getCopyrightNotice(): ?string;

    public function setCopyrightNotice(?string $copyrightNotice): void;

    public function getCopyrightHolder(): ?string;

    public function setCopyrightHolder(?string $copyrightHolder): void;
}
