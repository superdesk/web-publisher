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

use SWP\Component\TemplatesSystem\Twig\Node\GimmeListNode;

/**
 * Parser for gimme/endgimme blocks.
 */
class GimmeListTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideGimmeListEnd(\Twig_Token $token)
    {
        return $token->test('endgimmelist');
    }

    public function decideGimmeListFork(\Twig_Token $token)
    {
        return $token->test(['else', 'endgimmelist']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'gimmelist';
    }

    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $variable = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(\Twig_Token::NAME_TYPE, 'from');
        $collectionType = $this->parser->getExpressionParser()->parseAssignmentExpression();

        $collectionFilters = null;
        if ($stream->test(\Twig_Token::PUNCTUATION_TYPE, '|')) {
            $collectionFilters = $this->parser->getExpressionParser()->parsePostfixExpression($collectionType);
        }

        $parameters = null;
        if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'with')) {
            $parameters = $this->parser->getExpressionParser()->parseExpression();
        }

        $ifExpression = null;
        if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'if')) {
            $ifExpression = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideGimmeListFork']);
        if ($stream->next()->getValue() == 'else') {
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse([$this, 'decideGimmeListEnd'], true);
        } else {
            $else = null;
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new GimmeListNode($variable, $collectionType, $collectionFilters, $parameters, $ifExpression, $else, $body, $lineno, $this->getTag());
    }
}
