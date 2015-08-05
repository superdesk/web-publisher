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

use SWP\UpdaterBundle\Model\UpdatePackage;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Update manager.
 */
class UpdateManager extends AbstractManager
{
    const UPDATES_ENDPOINT = '/check.json';
    const CORE_ENDPOINT = '/core.json';
    const LATEST_VERSION_ENDPOINT = '/latest_version.json';

    const RESOURCE_CORE = 'core';

    /**
     * Available updates channels.
     *
     * @var array
     */
    private $channels = array('default', 'security', 'nightly');

    /**
     * Latest update.
     *
     * @var UpdatePackage
     */
    private $latestUpdate;

    /**
     * List of available updates.
     *
     * @var array
     */
    private $availableUpdates = array();

    /**
     * {@inheritdoc}
     */
    public function getAvailableUpdates($channel = '')
    {
        $response = $this->client->call(
            self::UPDATES_ENDPOINT,
            array(
                'coreVersion' => $this->getCurrentVersion(),
                'channel' => in_array($channel, $this->channels) ? $channel : $this->channels[0],
            )
        );

        if (isset($response['_items']) && !empty($response['_items'])) {
            foreach ($response['_items'] as $key => $resource) {
                foreach ($resource as $value) {
                    $this->availableUpdates[$key][] = new UpdatePackage($value);
                }
            }
        }

        if (empty($this->availableUpdates)) {
            throw new NotFoundHttpException('No update packages available.');
        }

        return $this->availableUpdates;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    /**
     * Gets the latest version.
     *
     * @return string|null Latest version
     */
    public function getLatestVersion()
    {
        $latestUpdate = $this->getLatestUpdate();
        if ($latestUpdate) {
            return $latestUpdate->getVersion();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestUpdate()
    {
        if (!$this->latestUpdate) {
            $response = $this->client->call(self::LATEST_VERSION_ENDPOINT);
            $this->latestUpdate = new UpdatePackage($response);
        }

        return $this->latestUpdate;
    }

    /**
     * Downloads core updates to the directory
     * defined in the config.yml. Defaults to: cache.
     */
    public function downloadCoreUpdates()
    {
        foreach ((array) $this->availableUpdates['core'] as $update) {
            $this->copyRemoteFile($update->url, $update->getVersion());
        }
    }

    /**
     * Downloads available updates to the app instance
     * (see temp_dir configuration option), by default to app's cache folder.
     *
     * @param string $resource Resource type (e.g. core, plugin etc.)
     *
     * @throws UnprocessableEntityHttpException When resource doesn't exist
     */
    public function download($resource)
    {
        $this->getAvailableUpdates();
        switch ($resource) {
            case self::RESOURCE_CORE:
                $this->downloadCoreUpdates();
                break;

            default:
                throw new UnprocessableEntityHttpException(sprintf(
                    'Resource "%s" doesn\'t exist!',
                    $resource
                ));
        }
    }

    /**
     * Apply available updates to the app by given resource.
     *
     * @param string $resource Resource type (e.g. core, plugin etc.)
     *
     * @throws UnprocessableEntityHttpException When resource doesn't exist
     */
    public function applyUpdates($resource)
    {
        $this->getAvailableUpdates();
        switch ($resource) {
            case self::RESOURCE_CORE:
                $this->updateCore();
                break;

            default:
                throw new UnprocessableEntityHttpException(sprintf(
                    'Resource "%s" doesn\'t exist!',
                    $resource
                ));
        }
    }

    /**
     * Updates the core by calling Updater "update" command.
     */
    public function updateCore()
    {
        $sortedVersions = $this->sortPackagesByVersion($this->availableUpdates['core']);
        foreach ($sortedVersions as $update) {
            $packageName = $update->getVersion().'.zip';
            $packagePath = $this->tempDir.'/'.$packageName;
            $this->addLogInfo('Started updating application\'s core...');
            if (!file_exists($packagePath)) {
                throw new NotFoundHttpException(sprintf(
                    'Update packge %s could not be found at %s',
                    $packageName,
                    $this->tempDir
                ));
            }

            $result = Updater::runUpdateCommand(array(
                'target' => $this->targetDir,
                'temp_dir' => $this->tempDir,
                'package_dir' => $packagePath,
            ));

            if ($result !== 0) {
                throw new \Exception('Could not update the instance.');
            }

            $this->addLogInfo('Successfully updated application\'s core...');

            $this->cleanUp($packagePath);
        }
    }

    private function cleanUp($packagePath)
    {
        $this->addLogInfo('Started cleaning up...');
        unlink($packagePath);
        $this->addLogInfo('Successfully cleaned up...');
    }
}
