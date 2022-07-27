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

    public function setUp(): void
    {
        parent::setUp();

        $this->loadCustomFixtures(['tenant']);
        $this->twig = $this->getContainer()->get('twig');
        $this->metaFactory = $this->getContainer()->get('swp_template_engine_context.factory.meta_factory');
    }

    public function testRenderLinkImpressionCount()
    {
        $this->assertEquals("<script type=\"text/javascript\" async>
window.addEventListener('load',function(){
function isInCurrentViewport(el) {
    const rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}


let arr = [], links = [], processedLinks = [], l = document.links;
const hostname = window.location.hostname;
var iterator = 0;
var breakpoint = 200;

var process = function process() {
  links = [];
  arr = [];
  // filter out section pages
  for(let i=0; i<l.length; i++) {
      const parts = l[i].pathname.split('/');
      if (parts.length > 2 && isInCurrentViewport(l[i]) && processedLinks.indexOf(l[i].href) === -1) {
          links.push(l[i]);
      }
  }
  // filter out links with data-article, add article id
  for(let i=0; i<links.length; i++) {
      const attr = links[i].dataset['article'];
      // if attribute not in array
      if(typeof attr !== 'undefined' && arr.indexOf(attr) === -1 && isInCurrentViewport(links[i]) && processedLinks.indexOf(links[i].href) === -1){
          arr.push(attr); 
          links.splice(i, 1);
      }
  }
  
  // filter out links different than current domain
  for(let i=0; i<links.length; i++){
      if(arr.indexOf(links[i].href) === -1 && links[i].href.indexOf(hostname) !== -1){
          arr.push(links[i].href);
      }
  }

  processedLinks = unique(processedLinks.concat(arr));
  
  if (arr.length > 0) {
      let xhr = new XMLHttpRequest();
      let read_date = new Date();
      let request_randomizer = \"&\" + read_date.getTime() + Math.random();
      xhr.open('POST', '/_swp_analytics?type=impression'+request_randomizer);
      xhr.setRequestHeader(\"Content-Type\", \"application/json\");
      xhr.send(JSON.stringify(arr));
  }
}

var countImpressions = function countImpressions() {
    let scrollDown = document.body.scrollTop || document.documentElement.scrollTop;
    if (scrollDown >= breakpoint) {
       process();
       breakpoint += 200;
       iterator++;
    }
}

var unique = function unique(array) {
    let a = array.concat();
    for(let i=0; i<a.length; ++i) {
        for(let j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
}

window.onscroll = function() {countImpressions()};
process();
})
</script>", $this->getRendered('{{ countArticlesImpressions() }}'));
    }

    public function testRenderPageViewCount()
    {
        $article = new Article();
        $article->setId('1');
        $articleMeta = $this->metaFactory->create($article);
        $this->assertEquals("<script type=\"text/javascript\" async>
window.addEventListener('load',function(){
var xhr = new XMLHttpRequest();
var read_date = new Date();
var request_randomizer = \"&\" + read_date.getTime() + Math.random();
xhr.open('GET', '/_swp_analytics?articleId=1'+request_randomizer+'&ref='+document.referrer);
xhr.send();
})
</script>", $this->getRendered('{{ countPageView(article) }}', ['article' => $articleMeta]));
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
