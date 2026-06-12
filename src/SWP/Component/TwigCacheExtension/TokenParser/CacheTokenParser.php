<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Twig Cache Extension.
 *
 * Twig 3 port of the abandoned asm89 twig/cache-extension (MIT).
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 */

namespace SWP\Component\TwigCacheExtension\TokenParser;

use SWP\Component\TwigCacheExtension\Node\CacheNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class CacheTokenParser extends AbstractTokenParser
{
    public function getTag(): string
    {
        return 'cache';
    }

    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $annotation = $this->parser->getExpressionParser()->parseExpression();
        $key = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideCacheEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new CacheNode($annotation, $key, $body, $lineno, $this->getTag());
    }

    public function decideCacheEnd(Token $token): bool
    {
        return $token->test('endcache');
    }
}
