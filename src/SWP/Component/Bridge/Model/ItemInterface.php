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

use Doctrine\Common\Collections\Collection;

/**
 * Interface ItemInterface.
 */
interface ItemInterface extends ContentInterface
{
    const TYPE_TEXT = 'text';

    const TYPE_FILE = 'file';

    const TYPE_PICTURE = 'picture';

    const TYPE_COMPOSITE = 'composite';

    const TYPE_VIDEO = 'video';

    const TYPE_AUDIO = 'audio';

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getBodyText();

    /**
     * @return string
     */
    public function getUsageTerms();

    /**
     * @param PackageInterface|void $package
     */
    public function setPackage(PackageInterface $package = null);

    /**
     * @param Collection $renditions
     */
    public function setRenditions(Collection $renditions);

    /**
     * @return Collection
     */
    public function getRenditions(): Collection;
}
