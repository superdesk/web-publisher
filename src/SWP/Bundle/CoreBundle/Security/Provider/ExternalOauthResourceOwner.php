<?php

namespace SWP\Bundle\CoreBundle\Security\Provider;

use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class ExternalOauthResourceOwner extends GenericResourceOwner
{
    use ArrayAccessorTrait;

    protected $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->getValueByKey($this->response, 'user_id');
    }

    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

    public function getName()
    {
        return $this->getValueByKey($this->response, 'name');
    }

    public function toArray()
    {
        return $this->response;
    }
}
