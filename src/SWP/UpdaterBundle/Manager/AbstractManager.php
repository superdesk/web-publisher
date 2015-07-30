<?php

namespace SWP\UpdaterBundle\Manager;

use SWP\UpdaterBundle\Client\ClientInterface;
use SWP\UpdaterBundle\Version\VersionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Updater\Console\Application;
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
     * @var App target directory.
     */
    protected $targetDir;

    /**
     * Construct.
     *
     * @param ClientInterface  $client  Client
     * @param VersionInterface $version Version
     */
    public function __construct(ClientInterface $client, VersionInterface $version, array $options = array())
    {
        $this->client = $client;
        $this->currentVersion = $version->getVersion();
        $this->tempDir = $options['temp_dir'];
        $this->targetDir = $options['target_dir'];
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
     * Runs command with given parameters.
     *
     * @param array $parameters Command parameters
     *
     * @return string Command output
     */
    protected function runCommand(array $parameters = array())
    {
        $input = new ArrayInput($parameters);
        $output = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        $app->run($input, $output);

        return $output->fetch();
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
