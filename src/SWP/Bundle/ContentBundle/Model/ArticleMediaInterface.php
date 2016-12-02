<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Interface ArticleMediaInterface.
 */
interface ArticleMediaInterface extends PersistableInterface
{
    const PATH_MEDIA = 'media';
    const PATH_RENDITIONS = 'renditions';

    /**
     * @return FileInterface
     */
    public function getFile();

    /**
     * @return FileInterface
     */
    public function getImage();

    /**
     * @return ArticleInterface
     */
    public function getArticle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return ArrayCollection
     */
    public function getRenditions();

    /**
     * @return string
     */
    public function getLocated();

    /**
     * @return string
     */
    public function getByLine();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $mediaId
     *
     * @return string
     */
    public static function handleMediaId($mediaId);
}
