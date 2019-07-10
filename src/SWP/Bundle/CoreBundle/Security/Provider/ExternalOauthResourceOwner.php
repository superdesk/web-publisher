<?php

namespace SWP\Bundle\CoreBundle\Security\Provider;

use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class ExternalOauthResourceOwner extends GenericResourceOwner
{
    use ArrayAccessorTrait;

    protected $response;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'sub');
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getValueByKey($this->response, 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->response;
    }
}
