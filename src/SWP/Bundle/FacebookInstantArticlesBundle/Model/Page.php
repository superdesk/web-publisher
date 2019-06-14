<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Model;

use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

class Page implements TimestampableInterface, PageInterface, EnableableInterface
{
    use TimestampableTrait;
    use EnableableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pageId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->setEnabled(true);
    }

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
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param string $pageId
     */
    public function setPageId(string $pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }
}
