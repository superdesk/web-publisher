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
let arr = [], links = [], l = document.links;
const hostname = window.location.hostname;

for(var i=0; i<l.length; i++) {const parts = l[i].pathname.split('/');if (parts.length > 2) {links.push(l[i])}}
for(var i=0; i<links.length; i++) {const attr = links[i].dataset['article'];if(typeof attr !== 'undefined' && arr.indexOf(attr) === -1){arr.push(attr); links.splice(i, 1);}}
for(var i=0; i<links.length; i++){if(arr.indexOf(links[i].href) === -1 && links[i].href.indexOf(hostname) !== -1){arr.push(links[i].href);}}

var xhr = new XMLHttpRequest();
var read_date = new Date();
var request_randomizer = "&" + read_date.getTime() + Math.random();
xhr.open('POST', '/_swp_analytics?type=impression'+request_randomizer);
xhr.setRequestHeader("Content-Type", "application/json");
xhr.send(JSON.stringify(arr));
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
