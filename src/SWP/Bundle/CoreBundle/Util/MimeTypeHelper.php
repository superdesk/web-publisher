<?php

namespace SWP\Bundle\CoreBundle\Util;

use Symfony\Component\Mime\MimeTypes;
class MimeTypeHelper
{
    /**
     * This helper function is used to solve a problems which are present in current symfony/mime package.
     *
     * For example for `js` extension this package return:
     *  - MimeTypes::getMimeTypes('js') === ['text/javascript','application/javascript','application/x-javascript']
     *  - MimeTypes::guessMimeType('js') === 'text/javascript'
     *
     * The desired output should be `application/javascript`.
     * In newer version of package (symfony 6) order is: ['application/javascript','application/x-javascript','text/javascript']
     * so this array_revers should not be needed.
     *
     * @param string $path
     * @param bool $all
     * @return array|string
     */
    public static function getByPath(string $path, bool $all = false): array|string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return self::getByExtension($ext, $all);
    }

    /**
     * @param string $path
     * @param bool $all
     * @return array|string
     */
    public static function getByExtension(string $path, bool $all = false): array|string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $mimeType = MimeTypes::getDefault();
        $types = $mimeType->getMimeTypes($ext);

        if (str_contains($types[0], 'text')) {
            $types = array_reverse($types);
        }

        return $all ? $types : str_replace('/x-', '/', $types[0]);
    }

    /**
     * @param string $mime
     * @param bool $all
     * @return array|string
     */
    public static function getExtensionByMimeType(string $mime, bool $all = false): array|string
    {
        $mimeType = MimeTypes::getDefault();
        $extensions = $mimeType->getExtensions($mime);

        return $all ? $extensions : $extensions[0];
    }
}