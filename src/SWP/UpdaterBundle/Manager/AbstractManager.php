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

use SWP\UpdaterBundle\Client\ClientInterface;
use SWP\UpdaterBundle\Version\VersionInterface;
use vierbergenlars\SemVer\version;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\UpdaterBundle\Model\UpdatePackage;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Abstract update manager.
 */
abstract class AbstractManager implements ManagerInterface
{
    /**
     * Current app version.
     *
     * @var string
     */
    protected $currentVersion;

    /**
     * The latest app version.
     *
     * @var string
     */
    protected $latestVersion = '0.0.0';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Temp directory to store update packages.
     */
    protected $tempDir;

    /**
     * @var App target directory.
     */
    protected $targetDir;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Construct.
     *
     * @param ClientInterface      $client  Client
     * @param VersionInterface     $version Version
     * @param array                $options An array of options
     * @param LoggerInterface|null $logger  Logger
     */
    public function __construct(
        ClientInterface $client,
        VersionInterface $version,
        array $options = array(),
        LoggerInterface $logger = null
    ) {
        $this->client = $client;
        $this->currentVersion = $version->getVersion();
        $this->tempDir = $options['temp_dir'];
        $this->targetDir = $options['target_dir'];
        $this->logger = $logger;
    }

    /**
     * Gets the logger instance.
     *
     * @return LoggerInterface Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Has the logger instance.
     *
     * @return bool
     */
    protected function hasLogger()
    {
        if ($this->logger) {
            return true;
        }

        return false;
    }

    /**
     * Adds logger info message.
     *
     * @param string $message Message
     */
    protected function addLogInfo($message)
    {
        if ($this->hasLogger()) {
            $this->getLogger()->info($message);
        }
    }

    /**
     * Copies remote file to the server where
     * the app is installed.
     *
     * @param string $fromUrl Remote file url
     * @param string $name    Copied file name
     *
     * @return bool True on success
     *
     * @throws NotFoundHttpException When file not found
     */
    protected function copyRemoteFile($fromUrl, $name)
    {
        try {
            $filePath = $this->tempDir.'/'.$name.'.zip';
            if (!file_exists($filePath)) {
                $this->client->get($fromUrl, [
                    'save_to' => $filePath,
                ]);

                $this->addLogInfo('Successfully downloaded update file: '.$filePath);

                return true;
            }
        } catch (\Exception $e) {
            throw new NotFoundHttpException(
                'Could not find file at the specified path: '.$fromUrl,
                $e,
                $e->getCode()
            );
        }
    }

    /**
     * Sorts an array of packages by version.
     * Descending order based on Semantic Versioning.
     *
     * @param array $array Array of objects
     */
    protected function sortPackagesByVersion(array $array = array())
    {
        usort($array, function ($first, $second) {
            if ($first instanceof UpdatePackage && $second instanceof UpdatePackage) {
                return version::compare($first->getVersion(), $second->getVersion());
            }
        });

        return $array;
    }
}
