<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System
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

use SWP\Component\TemplatesSystem\Twig\Node\GimmeNode;

/**
 * Parser for gimme/endgimme blocks.
 */
class GimmeTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token $token
     *
     * @return boolean
     */
    public function decideCacheEnd(\Twig_Token $token)
    {
        return $token->test('endgimme');
    }

    /**
     * {@inheritDoc}
     */
    public function getTag()
    {
        return 'gimme';
    }

    /**
     * {@inheritDoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $annotation = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $parameters = null;
        if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'with')) {
            $parameters = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideCacheEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new GimmeNode($annotation, $parameters, $body, $lineno, $this->getTag());
    }
}
