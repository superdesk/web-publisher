<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model\Event;

use SWP\Component\Bridge\Model\Event;

class Location implements LocationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $qcode;

    /**
     * @var Event
     */
    protected $event;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getQcode()
    {
        return $this->qcode;
    }

    /**
     * {@inheritdoc}
     */
    public function setQcode($qcode)
    {
        $this->qcode = $qcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * {@inheritdoc}
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }
}
