<?php

declare(strict_types=1);

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

use Doctrine\Persistence\ObjectManager;
use JsonSchema\Validator;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\Repository\UserRepository;
use SWP\Bundle\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ImportUserCommand extends Command
{
    private $schema = <<<'JSON'
{
  "definitions": {},
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "http://example.com/root.json",
  "type": "object",
  "title": "The Root Schema",
  "required": [
    "display_name",
    "name",
    "email",
    "created",
    "is_staff",
    "is_active"
  ],
  "properties": {
    "display_name": {
      "$id": "#/properties/display_name",
      "type": "string",
      "title": "The Display_name Schema",
      "default": "",
      "examples": [
        "doe"
      ],
      "pattern": "^(.*)$"
    },
    "name": {
      "$id": "#/properties/name",
      "type": "object",
      "title": "The Name Schema",
      "required": [
        "first",
        "last"
      ],
      "properties": {
        "first": {
          "$id": "#/properties/name/properties/first",
          "type": "string",
          "title": "The First Schema",
          "default": "",
          "examples": [
            "John"
          ],
          "pattern": "^(.*)$"
        },
        "last": {
          "$id": "#/properties/name/properties/last",
          "type": "string",
          "title": "The Last Schema",
          "default": "",
          "examples": [
            "Doe"
          ],
          "pattern": "^(.*)$"
        }
      }
    },
    "email": {
      "$id": "#/properties/email",
      "type": "string",
      "title": "The Email Schema",
      "default": "",
      "examples": [
        "doe@example.com"
      ],
      "pattern": "^(.*)$"
    },
    },
    "created": {
      "$id": "#/properties/created",
      "type": "string",
      "title": "The Created Schema",
      "default": "",
      "examples": [
        "2017-12-13T10:47:40"
      ],
      "pattern": "^(.*)$"
    },
    "is_staff": {
      "$id": "#/properties/is_staff",
      "type": "boolean",
      "title": "The Is_staff Schema",
      "default": false,
      "examples": [
        true
      ]
    },
    "is_active": {
      "$id": "#/properties/is_active",
      "type": "boolean",
      "title": "The Is_active Schema",
      "default": false,
      "examples": [
        true
      ]
    }
  }
}
JSON;

    protected static $defaultName = 'swp:import:user';

    /**
     * @var UserManipulator
     */
    private $userManipulator;

    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserManipulator $userManipulator, ObjectManager  $userManager, UserRepository $userRepository)
    {
        parent::__construct();
        $this->userManipulator = $userManipulator;
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setName('swp:import:user')
            ->setDescription('Import users from JSON files.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to JSON files.')
            ->setHelp(
                <<<'EOT'
The <info>swp:import:user</info> command imports users data from JSON files:

  <info>php %command.full_name% /home/jack/users/</info>

  <info>path</info> argument is the absolute path to the directory with the JSON files.

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $finder = new Finder();
        $finder->files()->in($path);

        $objectManager = $this->userManager;
        $userRepository = $this->userRepository;
        $validator = new Validator();

        foreach ($finder as $file) {
            $filePath = $file->getRelativePath();
            $data = json_decode($file->getContents(), true);
            $validator->validate($data, $this->schema);

            if (!$validator->isValid()) {
                $output->writeln("<bg=red;options=bold>$filePath skipped. JSON not valid.</>");

                continue;
            }

            $userEmail = strtolower($data['email']);
            $userId = null;

            if (isset($data['id'])) {
                $userId = $data['id'];
            }

            $existingUser = $userRepository->findOneByEmail($userEmail);

            if (null !== $existingUser) {
                $output->writeln("<bg=yellow;options=bold>$userEmail already exists. Skipping.</>");

                continue;
            }

            /** @var UserInterface $user */
            $user = $this->userManipulator->create($data['display_name'], uniqid('', true), $userEmail, $data['is_active'], $data['is_staff']);

            if (null !== $userId) {
                $user->setId($userId);
            }

            $user->setFirstName($data['name']['first']);
            $user->setLastName($data['name']['last']);
            $user->setCreatedAt(new \DateTime($data['created']));

            if (!$data['is_staff']) {
                $user->addRole(UserInterface::ROLE_DEFAULT);
            }

            $objectManager->persist($user);

            $output->writeln("<bg=green;options=bold>$userEmail imported.</>");
        }

        $objectManager->flush();

        $output->writeln('<bg=green;options=bold>Done.</>');

        return 0;
    }
}
