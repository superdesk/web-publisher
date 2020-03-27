<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;
use SWP\Component\Common\Model\DateTime;
use SWP\Component\Common\Model\TimestampableTrait;

class User extends BaseUser implements UserInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $about;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->createdAt = DateTime::getCurrentDateTime();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * {@inheritdoc}
     */
    public function setAbout(string $about = null)
    {
        $this->about = $about;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function setExternalId(string $externalId)
    {
        $this->externalId = $externalId;
    }
}
