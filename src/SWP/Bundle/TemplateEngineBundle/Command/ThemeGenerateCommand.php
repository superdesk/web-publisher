<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Command;

use SWP\Bundle\CoreBundle\Document\Tenant;
use SWP\Component\MultiTenancy\Exception\OrganizationNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

class ThemeGenerateCommand extends ContainerAwareCommand
{
    const THEMES_DIR = 'themes';
    const HOME_TWIG = 'home.html.twig';
    const THEME_CONFIG_JSON = 'theme.json';

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
                "The <info>%command.name%</info> command creates a skeleton theme in your application themes folder (app/themes)"
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
            $themeDir = implode(\DIRECTORY_SEPARATOR, [$this->getContainer()->get('kernel')->getRootDir(), self::THEMES_DIR, $tenant->getCode(), $themeName]);

            $fileSystem = new Filesystem();
            if ($fileSystem->exists($themeDir)) {
                $output->writeln('Theme '.$themeName.' already exists!');
                return;
            }

            $themeDir = $this->createSkeleton(new Filesystem(), $tenant->getCode(), $themeName);
            $this->writeConfigFile($themeDir, $themeName);
            $this->updateTenantReferenceToTheme($tenant, $themeName);
            $output->writeln('Theme '.$themeName.' has been generated successfully!');
        } catch (\Exception $e) {
            $output->writeln('Theme '.$themeName.' could not be generated!');
            $output->writeln('Stacktrace: '.$e->getMessage());
        }
    }

    /**
     * Gets the tenant based on the input - prompts a user to choose an existing tenant of the given organisation, or to create a new one
     *
     * @param $input
     * @param $output
     * @return mixed|null
     * @throws \Exception
     */
    protected function getTenant($input, $output)
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
            return null;
        }
    }

    /**
     * Creates folders and empty files of theme
     *
     * @param $tenantCode
     * @param $themeName
     */
    protected function createSkeleton(Filesystem $fileSystem, $tenantCode, $themeName)
    {
        $paths = [
            'phone/views/' . self::HOME_TWIG,
            'tablet/views/' . self::HOME_TWIG,
            'views/' . self::HOME_TWIG,
            'translations/messages.en.xlf',
            'translations/messages.de.xlf',
            'public/css',
            'public/json',
            'public/images',
            self::THEME_CONFIG_JSON
        ];

        $themeDir = $this->makePath($fileSystem,
            $this->getContainer()->get('kernel')->getRootDir(),
            [self::THEMES_DIR, $tenantCode, $themeName]);

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
     * Writes to the theme's config file
     *
     * @param $themeDir
     * @param $themeName
     */
    protected function writeConfigFile($themeDir, $themeName)
    {
        $configFileContents = $this->getConfigFileContents($themeName);
        file_put_contents($themeDir.\DIRECTORY_SEPARATOR.self::THEME_CONFIG_JSON, $configFileContents);
    }

    /**
     * @param $themeName
     * @return string
     */
    protected function getConfigFileContents($themeName)
    {
        $contents =
<<<'EOT'
{
    "name": "swp/%s",
    "title": "",
    "description": "",
    "authors": [
        {
            "name": "",
            "email": "",
            "homepage": "",
            "role": ""
        }
    ]
}
EOT;
        sprintf($contents, $themeName);
        return $contents;
    }

    /**
     * @param Tenant $tenant
     * @param $themeName
     */
    protected function updateTenantReferenceToTheme(Tenant $tenant, $themeName)
    {
        $tenant->setThemeName($themeName);
        $documentManager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $documentManager->flush();
    }

    /**
     * @param Filesystem $fileSystem
     * @param $baseDir
     * @param array $subDirs
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
     * @return string
     */
    protected function makeFile(FileSystem $filesystem, $baseDir, $fileName)
    {
        $fileName = $baseDir.\DIRECTORY_SEPARATOR.$fileName;
        $filesystem->touch($fileName);
        return $fileName;
    }
}
