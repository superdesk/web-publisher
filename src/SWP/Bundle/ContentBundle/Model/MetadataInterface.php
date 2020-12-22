<?php

declare(strict_types=1);

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

    public function addPlace(PlaceInterface $place): void;

    public function removePlace(PlaceInterface $place): void;

    public function hasPlace(PlaceInterface $place): bool;

    public function getProfile(): ?string;

    public function setProfile(?string $profile): void;

    public function getGuid(): ?string;

    public function setGuid(?string $guid): void;

    public function getUrgency(): ?int;

    public function setUrgency(?int $urgency): void;

    public function getPriority(): ?int;

    public function setPriority(?int $priority): void;

    public function getLocated(): ?string;

    public function setLocated(?string $located): void;

    public function getByline(): ?string;

    public function setByline(?string $byline): void;

    public function getLanguage(): ?string;

    public function setLanguage(?string $language): void;

    public function getEdNote(): ?string;

    public function setEdNote(?string $edNote): void;

    public function getGenre(): ?string;

    public function setGenre(?string $genre): void;

    public function getArticle(): ?ArticleInterface;

    public function setArticle(?ArticleInterface $article): void;
}
