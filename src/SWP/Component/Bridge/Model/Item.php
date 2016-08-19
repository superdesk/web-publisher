<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Bridge\Model;

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
     * Set package.
     *
     * @param \SWP\Component\Bridge\Model\Package $package
     *
     * @return Item
     */
    public function setPackage(\SWP\Component\Bridge\Model\Package $package = null)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package.
     *
     * @return \SWP\Component\Bridge\Model\Package
     */
    public function getPackage()
    {
        return $this->package;
    }
}
