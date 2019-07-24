<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Loader;

use Twig\Error\LoaderError;

final class NamespacedFilesystemTemplateLoader extends FilesystemTemplateLoader
{
    const MAIN_NAMESPACE = '__main__';

    private $paths = [
        self::MAIN_NAMESPACE => [],
    ];
    protected $errorCache = [];

    public function addPath(string $path, string $namespace): void
    {
        $this->paths[$namespace][] = rtrim($path, '/\\');
    }

    public function getNamespaces()
    {
        return array_keys($this->paths);
    }

    protected function findTemplate($name): string
    {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        list($namespace, $shortname) = $this->parseName($name);
        if (!isset($this->paths[$namespace])) {
            return parent::findTemplate($name);
        }

        foreach ($this->paths[$namespace] as $path) {
            if ($this->themeAssetProvider->hasFile($path.'/'.$shortname)) {
                $this->cache[$name] = $path.'/'.$shortname;

                return $this->cache[$name];
            }
        }

        return parent::findTemplate($name);
    }

    private function normalizeName($name)
    {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', $name));
    }

    private function parseName($name, $default = self::MAIN_NAMESPACE)
    {
        if (isset($name[0]) && '@' === $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new LoaderError(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
            }

            $namespace = substr($name, 1, $pos - 1);
            $shortname = substr($name, $pos + 1);

            return [$namespace, $shortname];
        }

        return [$default, $name];
    }
}
