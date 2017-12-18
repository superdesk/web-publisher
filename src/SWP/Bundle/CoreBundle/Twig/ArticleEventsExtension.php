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
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('countPageView', [$this, 'renderPageViewCount'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param Meta $meta
     *
     * @return string|void
     */
    public function renderPageViewCount(Meta $meta)
    {
        $jsTemplate = <<<'EOT'
<script type="text/javascript">
var xhr = new XMLHttpRequest();
var read_date = new Date();
var request_randomizer = "&" + read_date.getTime() + Math.random();
xhr.open('GET', '/_swp_analytics?articleId=%s'+request_randomizer);
xhr.send();
</script>
EOT;
        $article = $meta->getValues();
        if (!$article instanceof ArticleInterface) {
            return;
        }

        return sprintf($jsTemplate, $article->getId());
    }
}
