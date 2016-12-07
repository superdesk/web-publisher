<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ParsingPreviewController extends Controller
{
    /**
     * @Route("/facebook/instantarticles/preview/{articleId}", options={"expose"=true}, name="swp_fbia_preview_parsing")
     * @Method("GET")
     */
    public function previewAction($articleId)
    {
        $metaFactory = $this->container->get('swp_template_engine_context.factory.meta_factory');
        $articleProvider = $this->container->get('swp.provider.article');
        $templateParser = $this->container->get('swp_facebook.template_parser');

        $metaFactory->create($articleProvider->getOneById($articleId));
        $instantArticle = $templateParser->parse();

        dump($instantArticle);

        return new Response($instantArticle->render());
    }
}
