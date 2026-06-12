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

namespace SWP\Component\TwigCacheExtension\Node;

use SWP\Component\TwigCacheExtension\Extension\CacheExtension;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

class CacheNode extends Node
{
    private static int $cacheCount = 0;

    public function __construct(AbstractExpression $annotation, AbstractExpression $keyInfo, Node $body, int $lineno, string $tag = 'cache')
    {
        parent::__construct(
            ['annotation' => $annotation, 'key' => $keyInfo, 'body' => $body],
            [],
            $lineno,
            $tag
        );
    }

    public function compile(Compiler $compiler): void
    {
        $i = self::$cacheCount++;

        $extension = var_export(CacheExtension::class, true);

        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$swpCacheStrategy%d = $this->env->getExtension(%s)->getCacheStrategy();', $i, $extension)."\n")
            ->write(sprintf('$swpCacheKey%d = $swpCacheStrategy%d->generateKey(', $i, $i))
            ->subcompile($this->getNode('annotation'))
            ->raw(', ')
            ->subcompile($this->getNode('key'))
            ->raw(");\n")
            ->write(sprintf('$swpCacheBody%d = $swpCacheStrategy%d->fetchBlock($swpCacheKey%d);', $i, $i, $i)."\n")
            ->write(sprintf('if (false === $swpCacheBody%d) {', $i)."\n")
            ->indent()
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write(sprintf('$swpCacheBody%d = ob_get_clean();', $i)."\n")
            ->write(sprintf('$swpCacheStrategy%d->saveBlock($swpCacheKey%d, $swpCacheBody%d);', $i, $i, $i)."\n")
            ->outdent()
            ->write("}\n")
            ->write(sprintf('echo $swpCacheBody%d;', $i)."\n");
    }
}
