<?php

namespace SWP\UpdaterBundle\Manager;

use SWP\UpdaterBundle\Client\ClientInterface;
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
    protected $currentVersion = '0.0.0';

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
    protected $latestVersion = '';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Construct.
     *
     * @param ClientInterface $client Update server client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        // TODO
        // inject here Setting service to get the current app version.
    }

    /**
     * Checks for the latest version from the available ones.
     *
     * @param array $versions An array of versions
     *
     * @return bool
     */
    protected function checkForLatestVersion(array $versions)
    {
        $this->latestVersion = '0.0.0';
        foreach ($versions as $versionRaw => $packageDetails) {
            $semver = new version($versionRaw);
            if ($semver->valid() === null) {
                // could not parse version
                continue;
            }

            if (version::gt($versionRaw, $this->currentVersion)) {
                if (version::gt($versionRaw, $this->latestVersion)) {
                    $this->latestVersion = $versionRaw;
                }

                $this->updates[] = array_merge(array(
                    'version' => $versionRaw,
                ), (array) $packageDetails);
            }
        }

        $this->sortVersions();

        return $this->isNewVersionAvailable();
    }

    /**
     * Sorts an array of versions by values.
     * Descending order based on Semantic Versioning.
     */
    protected function sortVersions()
    {
        usort($this->updates, function ($first, $second) {
            return version::compare($first['version'], $second['version']);
        });
    }

    /**
     * Checks if a new version is available.
     *
     * @return bool
     */
    public function isNewVersionAvailable()
    {
        return version::gt($this->latestVersion, $this->currentVersion);
    }
}
