<?php

namespace spec\SWP\UpdaterBundle\Manager;

use PhpSpec\ObjectBehavior;
use SWP\UpdaterBundle\Client\GuzzleClient;
use Prophecy\Argument;
use SWP\UpdaterBundle\Model\UpdatePackage;

class UpdateManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\UpdaterBundle\Manager\UpdateManager');
        $this->shouldBeAnInstanceOf('SWP\UpdaterBundle\Manager\AbstractManager');
    }

    function let($client, $version)
    {
    	$options = array(
    		'temp_dir' => 'some/temp/dir',
        	'target_dir' => 'some/target/dir'
    	);

    	$client->beADoubleOf('SWP\UpdaterBundle\Client\ClientInterface');
        $version->beADoubleOf('SWP\UpdaterBundle\Version\VersionInterface');
        $this->beConstructedWith($client, $version, $options);
    }

    function it_should_throw_NotFoundHttpException_when_no_updates_found($client)
    {
        $client->call(Argument::Any(), Argument::Type('array'))->willReturn('{}');

        $this->shouldThrow('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
        	->duringGetAvailableUpdates();
    }

    function it_gets_available_updates($client)
    {
    	$fakeResponse = '{"_items":{"core":[{"version":"0.2.1","changelog":["commit1"]},{"version":"0.2.0"}]}}';
        $client->call(Argument::Any(), Argument::Type('array'))->willReturn($fakeResponse);

        $result = $this->getAvailableUpdates();
        $result->shouldHaveCount(1);
        $result->shouldBeArray();
        foreach ($result['core'] as $package) {
            $package->shouldHaveType('SWP\UpdaterBundle\Model\UpdatePackage');
        }
    }


}
