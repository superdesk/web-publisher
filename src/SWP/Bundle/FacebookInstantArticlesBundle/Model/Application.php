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

class Application implements TimestampableInterface, EnableableInterface, ApplicationInterface
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
    protected $appId;

    /**
     * @var string
     */
    protected $appSecret;

    /**
     * Application constructor.
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
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppId(string $appId)
    {
        $this->appId = $appId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppSecret(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }
}
