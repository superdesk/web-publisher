<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\EventListener;

use PhpSpec\ObjectBehavior;

class LinkRequestListenerSpec extends ObjectBehavior
{
    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $controllerResolver
     * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface               $urlMatcher
     */
    public function let($controllerResolver, $urlMatcher)
    {
        $this->beConstructedWith($controllerResolver, $urlMatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\ContentBundle\EventListener\LinkRequestListener');
    }
}
