<?php

/**
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
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

class Item extends BaseContent implements ItemInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $bodyText;

    /**
     * Collection.
     */
    protected $renditions;

    /**
     * @var string
     */
    protected $usageTerms;

    /**
     * @var ArrayCollection
     */
    public $items;

    /**
     * @var Package
     */
    protected $package;

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param ArrayCollection $renditions
     */
    public function setRenditions($renditions)
    {
        $this->renditions = $renditions;
    }

    /**
     * @return ArrayCollection
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getBodyText()
    {
        return $this->bodyText;
    }

    /**
     * @param string $bodyText
     */
    public function setBodyText($bodyText)
    {
        $this->bodyText = $bodyText;
    }

    /**
     * @return string
     */
    public function getUsageTerms()
    {
        return $this->usageTerms;
    }

    /**
     * @param string $usageTerms
     */
    public function setUsageTerms($usageTerms)
    {
        $this->usageTerms = $usageTerms;
    }

    /**
     * Set package.
     *
     * @param PackageInterface|void $package
     *
     * @return Item
     */
    public function setPackage(PackageInterface $package = null)
    {
        $this->package = $package;
    }

    /**
     * Get package.
     *
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }
}
