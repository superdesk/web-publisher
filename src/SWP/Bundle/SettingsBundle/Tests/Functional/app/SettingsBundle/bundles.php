<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

return [
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new Symfony\Bundle\MonologBundle\MonologBundle(),
    new Liip\FunctionalTestBundle\LiipFunctionalTestBundle(),

    new SWP\Bundle\StorageBundle\SWPStorageBundle(),
    new SWP\Bundle\SettingsBundle\SWPSettingsBundle(),
];
