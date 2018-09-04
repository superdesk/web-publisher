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

namespace SWP\Bundle\CoreBundle\Tests\Twig;

use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\FixturesBundle\WebTestCase;

class ArticleEventsExtensionTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    private $metaFactory;

    public function setUp()
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant']);
        $this->twig = $this->getContainer()->get('twig');
        $this->metaFactory = $this->getContainer()->get('swp_template_engine_context.factory.meta_factory');
    }

    public function testRenderLinkImpressionCount()
    {
        $this->assertEquals("<script type=\"text/javascript\">
var arr = [], l = document.links;
for(var i=0; i<l.length; i++) {
  if(arr.indexOf(l[i].href) === -1){arr.push(l[i].href);}
}
var xhr = new XMLHttpRequest();
var read_date = new Date();
var request_randomizer = \"&\" + read_date.getTime() + Math.random();
xhr.open('POST', '/_swp_analytics?type=impression'+request_randomizer);
xhr.setRequestHeader(\"Content-Type\", \"application/json\");
xhr.send(JSON.stringify(arr));
</script>", $this->getRendered('{{ countArticlesImpressions() }}'));
    }

    public function testRenderPageViewCount()
    {
        $article = new Article();
        $article->setId('1');
        $articleMeta = $this->metaFactory->create($article);
        $this->assertEquals("<script type=\"text/javascript\">
var xhr = new XMLHttpRequest();
var read_date = new Date();
var request_randomizer = \"&\" + read_date.getTime() + Math.random();
xhr.open('GET', '/_swp_analytics?articleId=1'+request_randomizer);
xhr.send();
</script>", $this->getRendered('{{ countPageView(article) }}', ['article' => $articleMeta]));
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
