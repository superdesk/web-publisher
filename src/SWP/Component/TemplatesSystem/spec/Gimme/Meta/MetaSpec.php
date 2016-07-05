<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\TemplatesSystem\Gimme\Meta;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Yaml\Parser;

class MetaSpec extends ObjectBehavior
{
    public function let()
    {
        if (!is_readable(__DIR__.'/Resources/meta/article.yml')) {
            throw new \InvalidArgumentException('Configuration file is not readable for parser');
        }
        $yaml = new Parser();
        $configuration = $yaml->parse(file_get_contents(__DIR__.'/Resources/meta/article.yml'));

        $this->beConstructedWith($configuration, '{
            "title": "New article",
            "keywords": "lorem, ipsum, dolor, sit, ame",
            "dont_expose_it": "this should be not exposed"
        }');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\TemplatesSystem\Gimme\Meta\Meta');
    }

    public function it_should_show_title_when_printed()
    {
        $this->__toString()->shouldReturn('New article');
    }
}
