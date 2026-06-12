<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\Pagination;

use Knp\Component\Pager\ArgumentAccess\ArgumentAccessInterface;

/**
 * Argument access without a request: pagination arguments are passed
 * explicitly to PaginatorInterface::paginate() by the repositories.
 */
final class NullArgumentAccess implements ArgumentAccessInterface
{
    /**
     * @var array<string, string|int|float|bool|null>
     */
    private array $arguments = [];

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->arguments);
    }

    public function get(string $name): string|int|float|bool|null
    {
        return $this->arguments[$name] ?? null;
    }

    public function set(string $name, string|int|float|bool|null $value): void
    {
        $this->arguments[$name] = $value;
    }
}
