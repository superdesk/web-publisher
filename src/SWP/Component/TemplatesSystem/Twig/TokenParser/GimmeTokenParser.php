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

use SWP\Component\TemplatesSystem\Twig\Node\GimmeNode;

/**
 * Parser for gimme/endgimme blocks.
 */
class GimmeTokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    /**
     * @param \Twig\Token $token
     *
     * @return bool
     */
    public function decideCacheEnd(\Twig\Token $token)
    {
        return $token->test('endgimme');
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'gimme';
    }

    /**
     * {@inheritdoc}
     */
    public function parse(\Twig\Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $annotation = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $parameters = null;
        if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'with')) {
            $parameters = $this->parser->getExpressionParser()->parseExpression();
        }

        $ignoreContext = null;
        if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'ignoreContext')) {
            if ($stream->test(\Twig\Token::PUNCTUATION_TYPE, '[')) {
                $ignoreContext = $this->parser->getExpressionParser()->parseExpression();
            } else {
                $ignoreContext = new \Twig\Node\Expression\ArrayExpression([], $token->getLine());
            }
        }

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideCacheEnd'], true);
        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        return new GimmeNode($annotation, $parameters, $ignoreContext, $body, $lineno, $this->getTag());
    }
}
