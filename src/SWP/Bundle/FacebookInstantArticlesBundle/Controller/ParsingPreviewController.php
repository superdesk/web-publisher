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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SWP\Bundle\CoreBundle\ContentListEvents;
use SWP\Bundle\CoreBundle\Event\ContentListEvent;
use SWP\Bundle\CoreBundle\Model\ContentListItem;
use SWP\Bundle\FacebookInstantArticlesBundle\Parser\TemplateParser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParsingPreviewController extends Controller
{
    /**
     * @Route("/facebook/instantarticles/preview/{articleId}/{feedId}", options={"expose"=true}, name="swp_fbia_preview_parsing")
     * @Method("GET")
     * @Template()
     */
    public function previewAction($articleId, $feedId = null)
    {
        /** @var TemplateParser $templateParser */
        $templateParser = $this->get('swp_facebook.template_parser');
        $article = $this->get('swp.provider.article')->getOneById($articleId);
        $this->get('swp_template_engine_context.factory.meta_factory')->create($article);
        $instantArticle = $templateParser->parse();

        $contentList = $this->get('swp.repository.content_list')->findOneById(1);
        $contentListItem = new ContentListItem();
        $contentListItem->setContent($article);
        $contentListItem->setContentList($contentList);
        $this->get('event_dispatcher')->dispatch(ContentListEvents::POST_ITEM_ADD, new ContentListEvent(
            $contentList,
            $contentListItem
        ));

        return $this->render('SWPFacebookInstantArticlesBundle:ParsingPreview:preview.html.twig', [
            'instantArticle' => $instantArticle,
            'warnings' => $templateParser->getTransformer()->getWarnings(),
        ]);
    }
}
