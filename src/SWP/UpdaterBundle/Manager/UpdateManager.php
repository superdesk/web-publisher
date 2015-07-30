<?php

namespace SWP\UpdaterBundle\Manager;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use SWP\UpdaterBundle\Model\UpdatePackage;

/**
 * Update manager.
 */
class UpdateManager extends AbstractManager
{
    const UPDATES_ENDPOINT = '/check.json';
    const CORE_ENDPOINT = '/core.json';
    const LATEST_VERSION_ENDPOINT = '/check2.json';

    const CORE_DOWNLOAD_ACTION = 'core_download';
    const CORE_UPDATE_ACTION = 'core_update';

    protected $forceDownload = false;

    /**
     * {@inheritdoc}
     */
    public function checkUpdates()
    {
        if (!empty($this->getUpdatesToApply())) {
            return $this->getUpdatesToApply();
        }

        $response = $this->client->call(
            self::UPDATES_ENDPOINT,
            ['coreVersion' => $this->getCurrentVersion()]
        );

        if (isset($response['_items']) && !empty($response['_items'])) {
            foreach ($response['_items'] as $key => $resource) {
                foreach ($resource as $value) {
                    $this->updates[$key][] = new UpdatePackage($value);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatesToApply()
    {
        return $this->updates;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestVersion()
    {
        $response = $this->client->call(self::LATEST_VERSION_ENDPOINT);

        return $response;
    }

    /**
     * Downloads core updates to the directory
     * defined in the config.yml. Defaults to: cache.
     */
    public function downloadCoreUpdates()
    {
        foreach ((array) $this->updates['core'] as $update) {
            $this->copyRemoteFile($update['url'], $update['version']);
        }
    }

    /**
     * Apply all available updates
     * to the current app instance.
     *
     * @param array $parameters Parameters
     */
    public function updateInstance(array $parameters = array())
    {
        $this->validateParameters($parameters);
        $this->checkUpdates();
        switch ($parameters['action']) {
            case self::CORE_DOWNLOAD_ACTION:
                $this->downloadCoreUpdates();
                break;
            case self::CORE_UPDATE_ACTION:
                $this->updateCore();
                break;

            default:
                throw new \Exception('Invalid action.');
        }
    }

    public function validateParameters(array $parameters = array())
    {
        $requiredParameters = array('action');
        foreach ($requiredParameters as $param) {
            if (!array_key_exists($param, $parameters)) {
                throw new \Exception('Wrong parameters.');
            }
        }
    }

    public function updateCore()
    {
        /*$input = new ArrayInput(array(
            'target' => __DIR__.'/../../sample_app',
            'temp_dir' => __DIR__.'/../../sample_app/cache',
            'package_dir' => __DIR__.'/../../packages/update-4.3.1.zip',
            '--rollback' => false,
        ));

        $output = new NullOutput();
                $app = new \Updater\Console\Application();
                var_dump($app->get('update'));die;*/
    }
}
