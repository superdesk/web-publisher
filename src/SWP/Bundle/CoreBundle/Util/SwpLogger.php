<?php

namespace SWP\Bundle\CoreBundle\Util;

class SwpLogger
{

    public static function log(string $message,bool $echo = false, string $fileName = 'swp.log')
    {
        $trace = debug_backtrace()[1];
        $message = '[' . date('Y-m-d H:i:s') . '][' . $trace['class'] . '::' . $trace['function'] . '] ' . $message . PHP_EOL;

        if (!empty($fileName)) {
            file_put_contents('/tmp/' . $fileName, $message, FILE_APPEND);
        }
        if ($echo) {
            echo $message;
        }
    }
}