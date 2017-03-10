<?php

/*
 * This file is part of the Superdesk Web Publisher.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

use Liip\RMT\Context;
use Liip\RMT\Action\BaseAction;

/**
 * Class UpdateApplicationVersionCurrentVersion.
 *
 * Custom pre-release action for updating the version number in the application
 */
class UpdateApplicationVersion extends BaseAction
{
    public function getTitle()
    {
        return 'Application version update';
    }

    public function execute()
    {
        $newVersion = Context::getParam('new-version');

        // newscoop/library/Newscoop/Version.php
        $appFile = realpath(__DIR__.'/../src/SWP/Bundle/CoreBundle/Version/Version.php');
        Context::get('output')->writeln("Updating version [<yellow>$newVersion</yellow>] in $appFile: ");
        $fileContent = file_get_contents($appFile);
        $fileContent = preg_replace('/(.*protected \$version = .*;)/', '    protected $version = \''.$newVersion.'\';', $fileContent);
        $fileContent = preg_replace('/(.*protected \$releaseDate = .*;)/', '    protected $releaseDate = \''.date('Y-m-d').'\';', $fileContent);
        file_put_contents($appFile, $fileContent);

        $this->confirmSuccess();
    }
}
