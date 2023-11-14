<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RedirectExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * RedirectExtension constructor.
     *
     * @param RequestStack    $requestStack
     * @param RouterInterface $router
     */
    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('redirect', array($this, 'redirect')),
            new TwigFunction('notFound', array($this, 'redirectToNotFound')),
        );
    }

    /**
     * @param mixed $route
     * @param int   $code
     * @param array $parameters
     *
     * @return mixed
     */
    public function redirect($route, $code = 301, $parameters = [])
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            return $route;
        }

        try {
            $url = $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (RouteNotFoundException $e) {
            //todo: check if it's absolut url or path and return url
            return $route;
        }

        $request->attributes->set('_swp_redirect', [
            'url' => $url,
            'code' => $code,
        ]);

        return $route;
    }

    /**
     * @param string $message
     */
    public function redirectToNotFound(string $message)
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            return;
        }

        $request->attributes->set('_swp_not_found', $message);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }
}
