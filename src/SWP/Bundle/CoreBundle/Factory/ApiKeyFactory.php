<?php

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

namespace SWP\Bundle\CoreBundle\Factory;

use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;

class ApiKeyFactory
{
    /**
     * @var string
     */
    private $className;

    /**
     * Factory constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param UserInterface $user
     * @param string|null   $apiKeyValue
     *
     * @return mixed
     */
    public function create($user, $apiKeyValue = null)
    {
        /** @var ApiKeyInterface $apiKey */
        $apiKey = new $this->className();

        if (null === $apiKeyValue) {
            $apiKeyValue = base64_encode(random_bytes(36).':');
        }

        $apiKey->setApiKey($apiKeyValue);
        $apiKey->setUser($user);

        $validDate = new \DateTime();
        $validDate->modify('+4 hours');
        $apiKey->setValidTo($validDate);

        return $apiKey;
    }
}
