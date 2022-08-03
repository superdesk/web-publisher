<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SWP\Bundle\UserBundle\Command\CreateUserCommand as BaseCreateUserCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends BaseCreateUserCommand
{
    protected static $defaultName = 'swp:user:create';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);
        } catch (UniqueConstraintViolationException $e) {
            $output->writeln(sprintf('<error>User with username %s already exists!</error>', $input->getArgument('username')));
            return 1;
        }

        return 0;
    }
}
