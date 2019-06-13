<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArticleEventsExtension extends AbstractExtension
{
    protected $analyticsHost;

    public function __construct(string $analyticsHost = null)
    {
        $this->analyticsHost = $analyticsHost;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('countPageView', [$this, 'renderPageViewCount'], ['is_safe' => ['html']]),
            new TwigFunction('countArticlesImpressions', [$this, 'renderLinkImpressionCount'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param Meta $meta
     *
     * @return string|void
     */
    public function renderLinkImpressionCount()
    {
        $jsTemplate = <<<'EOT'
<script type="text/javascript">
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
      let request_randomizer = "&" + read_date.getTime() + Math.random();
      xhr.open('POST', '/_swp_analytics?type=impression'+request_randomizer);
      xhr.setRequestHeader("Content-Type", "application/json");
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

</script>
EOT;

        if (null !== $this->analyticsHost) {
            $jsTemplate = str_replace('/_swp_analytics', $this->analyticsHost.'/_swp_analytics', $jsTemplate);
        }

        return $jsTemplate;
    }

    /**
     * @param Meta $meta
     *
     * @return string|void
     */
    public function renderPageViewCount(Meta $meta = null)
    {
        if (null === $meta) {
            return;
        }

        $jsTemplate = <<<'EOT'
<script type="text/javascript">
var xhr = new XMLHttpRequest();
var read_date = new Date();
var request_randomizer = "&" + read_date.getTime() + Math.random();
xhr.open('GET', '/_swp_analytics?articleId=%s'+request_randomizer+'&ref='+document.referrer);
xhr.send();
</script>
EOT;
        $article = $meta->getValues();
        if (!$article instanceof ArticleInterface) {
            return;
        }

        if (null !== $this->analyticsHost) {
            $jsTemplate = str_replace('/_swp_analytics', $this->analyticsHost.'/_swp_analytics', $jsTemplate);
        }

        return sprintf($jsTemplate, $article->getId());
    }
}
