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
namespace SWP\Component\TemplatesSystem\Twig\TokenParser;

use SWP\Component\TemplatesSystem\Twig\Node\ContainerNode;

/**
 * Parser for container/endcontainer blocks.
 */
class ContainerTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideEnd(\Twig_Token $token)
    {
        return $token->test('endcontainer');
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'container';
    }

    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $name = $this->parser->getExpressionParser()->parseExpression();

        $parameters = null;
        if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'with')) {
            $parameters = $this->parser->getExpressionParser()->parseExpression();
        }


        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideEnd'], true);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new ContainerNode($name, $parameters, $body, $lineno, $this->getTag());
    }
}
