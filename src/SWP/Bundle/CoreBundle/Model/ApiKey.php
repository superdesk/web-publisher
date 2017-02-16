<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Common\Model\TimestampableTrait;

class ApiKey implements ApiKeyInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var \DateTime
     */
    protected $validTo;

    /**
     * ApiKey constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getValidTo(): \DateTime
    {
        return $this->validTo;
    }

    /**
     * @param \DateTime $validTo
     */
    public function setValidTo(\DateTime $validTo)
    {
        $this->validTo = $validTo;
    }

    public function extendValidTo()
    {
        $extendedDate = new \DateTime();
        $extendedDate->modify('+30 minutes');

        if ($this->getValidTo() < $extendedDate) {
            $this->setValidTo($extendedDate);
        }
    }
}
