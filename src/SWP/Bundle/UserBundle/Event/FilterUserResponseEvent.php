<?php

/*
 * This file is part of the SWPUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SWP\Bundle\UserBundle\Event;

//use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterUserResponseEvent extends UserEvent
{
    private $response;

    /**
     * FilterUserResponseEvent constructor.
     */
    public function __construct(UserInterface $user, Request $request, Response $response)
    {
        parent::__construct($user, $request);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a new response object.
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
