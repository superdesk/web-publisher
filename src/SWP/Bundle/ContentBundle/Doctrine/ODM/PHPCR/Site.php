<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use SWP\Bundle\MultiTenancyBundle\Document\Site as BaseSite;
use SWP\Component\Storage\Model\PersistableInterface;

class Site extends BaseSite implements PersistableInterface
{
    /**
     * Gets the value of homepage.
     *
     * @return mixed
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Sets the value of homepage.
     *
     * @param Route $homepage the homepage
     */
    public function setHomepage(Route $homepage)
    {
        $this->homepage = $homepage;
    }

    public function __toString()
    {
        return $this->id;
    }
}
