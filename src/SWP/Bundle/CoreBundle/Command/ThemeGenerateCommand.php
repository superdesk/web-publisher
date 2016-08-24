<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Command;

use SWP\Bundle\CoreBundle\Document\Tenant;
use SWP\Component\MultiTenancy\Exception\OrganizationNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class ThemeGenerateCommand extends ContainerAwareCommand
{
    const HOME_TWIG = 'home.html.twig';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('theme:generate')
            ->setDescription('Creates basic theme structure with routes and empty templates.')
            ->addArgument(
                'organizationName',
                InputArgument::REQUIRED,
                'Organization Name',
                null
            )
            ->addArgument(
                'themeName',
                InputArgument::REQUIRED,
                'Theme Name',
                null
            )
            ->setHelp(
                'The <info>%command.name%</info> command creates a skeleton theme in your application themes folder (app/themes)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $tenant = $this->getTenant($input, $output);
            if (null === $tenant) {
                return;
            }

            $themeName = $input->getArgument('themeName');

            $themeDir = $this->createSkeleton(new Filesystem(), $tenant->getCode(), $themeName);
            $this->writeConfigFile($input, $output, $tenant, $themeDir, $themeName);
            $this->updateTenantReferenceToTheme($tenant, $themeName);
            $output->writeln('Theme '.$themeName.' has been generated successfully!');
        } catch (\Exception $e) {
            $output->writeln('Theme '.$themeName.' could not be generated!');
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Gets the tenant based on the input - prompts a user to choose an existing tenant of the given organisation, or to create a new one.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return mixed|null
     *
     * @throws \Exception
     */
    protected function getTenant(InputInterface $input, OutputInterface $output)
    {
        $organizationRepository = $this->getContainer()->get('swp.repository.organization');
        $organizationName = $input->getArgument('organizationName');
        $organization = $organizationRepository->findOneByName($organizationName);
        if (null === $organization) {
            throw new OrganizationNotFoundException($organizationName);
        }

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Create new tenant?', false, '/^(y|j)/i');
        if (!$helper->ask($input, $output, $question)) {
            $tenants = $organization->getTenants()->toArray();
            $numTenants = count($tenants);
            if (!$numTenants) {
                throw new \Exception('Organization has no tenants');
            }

            $tenant = reset($tenants);
            if ($numTenants > 1) {
                $tenantNames = array_map(function ($tenant) {
                    return $tenant->getName();
                }, $tenants);

                $tenants = array_combine($tenantNames, $tenants);

                $question = new ChoiceQuestion(
                    'Please select a tenant',
                    $tenantNames,
                    $tenantNames[0]
                );
                $question->setErrorMessage('Name %s is not an option');

                $tenantName = $helper->ask($input, $output, $question);
                $tenant = $tenants[$tenantName];
            }

            return $tenant;
        } else {
            $output->writeln('Creation of tenant here still to be implemented - you can create a tenant using the swp:tenant:create command');
        }
    }

    /**
     * Creates folders and empty files of theme.
     *
     * @param $tenantCode
     * @param $themeName
     */
    protected function createSkeleton(Filesystem $fileSystem, $tenantCode, $themeName)
    {
        $configFilename = $this->getContainer()->getParameter('swp.theme.configuration.filename');
        $themesDir = $this->getContainer()->getParameter('swp.theme.configuration.default_directory');

        $paths = [
            'phone/views/'.self::HOME_TWIG,
            'tablet/views/'.self::HOME_TWIG,
            'views/index.html.twig',
            'translations/messages.en.xlf',
            'translations/messages.de.xlf',
            'public/css',
            'public/js',
            'public/images',
            $configFilename,
        ];

        $themeDir = implode(\DIRECTORY_SEPARATOR, [$themesDir, $tenantCode, $themeName]);
        if ($fileSystem->exists($themeDir)) {
            throw new \Exception('Theme '.$themeName.' already exists!');
        }

        $this->makePath($fileSystem, $themesDir, [$tenantCode, $themeName]);

        foreach ($paths as $path) {
            $elements = explode(\DIRECTORY_SEPARATOR, $path);

            $file = null;
            if (strpos(end($elements), '.')) {
                $file = array_pop($elements);
            }

            $path = $this->makePath($fileSystem, $themeDir, $elements);
            if (null !== $file) {
                $this->makeFile($fileSystem, $path, $file);
            }
        }

        return $themeDir;
    }

    /**
     * Writes to the theme's config file.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Tenant          $tenant
     * @param $themeDir
     * @param $themeName
     */
    protected function writeConfigFile(InputInterface $input, OutputInterface $output, Tenant $tenant, $themeDir, $themeName)
    {
        $output->writeln('To generate config file, please provide a few values.');

        $values = $this->getValuesFromUser($input,
            $output,
            [
                'title' => $themeName,
                'description' => $tenant->getOrganization()->getName().' '.$themeName.' theme',
                'author name' => 'anon',
                'author email' => 'anon',
                'author homepage' => 'homepage',
                'author role' => 'anon',
            ]
        );
        array_unshift($values, sprintf('swp/%s', $themeName));

        $contents =
<<<'EOT'
{
    "name": "%s",
    "title": "%s",
    "description": "%s",
    "authors": [
        {
            "name": "%s",
            "email": "%s",
            "homepage": "%s",
            "role": "%s"
        }
    ]
}
EOT;
        $contents = vsprintf($contents, $values);
        $configFilename = $this->getContainer()->getParameter('swp.theme.configuration.filename');

        file_put_contents($themeDir.\DIRECTORY_SEPARATOR.$configFilename, $contents);
    }

    /**
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @param array           $keys
     */
    protected function getValuesFromUser(InputInterface $input, OutputInterface $output, array $keysAndDefaults)
    {
        $results = [];
        $helper = $this->getHelper('question');
        foreach ($keysAndDefaults as $key => $default) {
            $question = new Question($key.': ', $default);
            $results[$key] = $helper->ask($input, $output, $question);
        }

        return $results;
    }

    /**
     * @param Tenant $tenant
     * @param $themeName
     */
    protected function updateTenantReferenceToTheme(Tenant $tenant, $themeName)
    {
        $tenant->setThemeName(sprintf('swp/%s', $themeName));
        $documentManager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $documentManager->flush();
    }

    /**
     * @param Filesystem $fileSystem
     * @param $baseDir
     * @param array $subDirs
     *
     * @return string
     */
    protected function makePath(Filesystem $fileSystem, $baseDir, array $subDirs)
    {
        $path = $baseDir;
        foreach ($subDirs as $dir) {
            $path .= \DIRECTORY_SEPARATOR.$dir;
            $fileSystem->mkdir($path);
        }

        return $path;
    }

    /**
     * @param Filesystem $filesystem
     * @param $baseDir
     * @param $fileName
     *
     * @return string
     */
    protected function makeFile(FileSystem $filesystem, $baseDir, $fileName)
    {
        $fileName = $baseDir.\DIRECTORY_SEPARATOR.$fileName;
        $filesystem->touch($fileName);

        return $fileName;
    }
}
