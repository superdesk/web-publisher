<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\UpdaterBundle\Manager;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Updater\Console\Application;

/**
 * Updater wrapper.
 */
class Updater
{
    const UPDATE_COMMAND = 'update';

    /**
     * Runs update command with given parameters.
     *
     * @param array $parameters Command parameters
     *
     * @return string Command output
     */
    public static function runUpdateCommand(array $parameters = array())
    {
        $parameters['command'] = self::UPDATE_COMMAND;

        return self::runCommand($parameters);
    }

    private static function runCommand(array $parameters = array())
    {
        $input = new ArrayInput($parameters);
        $output = new NullOutput();
        $app = new Application();
        $app->setAutoExit(false);

        return $app->run($input, $output);
    }
}
