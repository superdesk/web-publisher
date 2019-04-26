<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Twig\TokenParser;

use SWP\Component\TemplatesSystem\Twig\Node\ContainerNode;

/**
 * @deprecated since 2.0, will be removed in 3.0
 * Parser for container/endcontainer blocks.
 */
class ContainerTokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    /**
     * @param \Twig\Token $token
     *
     * @return bool
     */
    public function decideEnd(\Twig\Token $token)
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
    public function parse(\Twig\Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $name = $this->parser->getExpressionParser()->parseExpression();

        $parameters = null;
        if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'with')) {
            $parameters = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideEnd'], true);

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        return new ContainerNode($name, $parameters, $body, $lineno, $this->getTag());
    }
}
