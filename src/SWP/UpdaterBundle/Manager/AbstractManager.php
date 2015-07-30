<?php

namespace SWP\UpdaterBundle\Manager;

use SWP\UpdaterBundle\Client\ClientInterface;
use SWP\UpdaterBundle\Version\VersionInterface;
use vierbergenlars\SemVer\version;

/**
 * Abstract update manager.
 *
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
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
     * List of available updates.
     *
     * @var array
     */
    protected $updates = array();

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
     * Construct.
     *
     * @param ClientInterface  $client  Client
     * @param VersionInterface $version Version
     */
    public function __construct(ClientInterface $client, VersionInterface $version, $tempDir)
    {
        $this->client = $client;
        $this->currentVersion = $version->getVersion();
        $this->tempDir = $tempDir;
    }

    /**
     * Copies remote file to the server where
     * the app is installed.
     *
     * @param string $fromUrl Remote file url
     * @param string $version Copied file name
     *
     * @return bool
     */
    protected function copyRemoteFile($fromUrl, $name)
    {
        try {
            $this->client->get($fromUrl, [
                'save_to' => $this->tempDir.'/'.$name.'.zip',
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Checks if a new version is available.
     *
     * @return bool
     */
    protected function isNewVersionAvailable()
    {
        return version::gt($this->latestVersion, $this->currentVersion);
    }
}
