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
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidTo(): \DateTime
    {
        return $this->validTo;
    }

    /**
     * {@inheritdoc}
     */
    public function setValidTo(\DateTime $validTo)
    {
        $this->validTo = $validTo;
    }

    /**
     * {@inheritdoc}
     */
    public function extendValidTo()
    {
        $extendedDate = new \DateTime();
        $extendedDate->modify('+4 hours');

        if ($this->getValidTo() < $extendedDate) {
            $this->setValidTo($extendedDate);
        }
    }
}
