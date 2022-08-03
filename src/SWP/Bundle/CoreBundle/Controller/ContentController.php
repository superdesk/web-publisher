<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends AbstractController
{

  public function __construct() {
  }

  public function renderPageAction(string $contentTemplate): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');

        return $this->render($contentTemplate, [], $response);
    }
}
