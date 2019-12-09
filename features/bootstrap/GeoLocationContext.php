<?php

declare(strict_types=1);

use Behat\Mink\Element\ElementInterface;
use Behatch\Context\BaseContext;
use Behatch\HttpCall\Request;
use SWP\Behat\Service\GeoIPReaderAdapterStub;

final class GeoLocationContext extends BaseContext
{
    /** @var Request */
    private $request;

    /** @var GeoIPReaderAdapterStub */
    protected $geoIpReader;

    public function __construct(Request $request, GeoIPReaderAdapterStub $geoIpReader)
    {
        $this->request = $request;
        $this->geoIpReader = $geoIpReader;
    }

    /**
     * @Given I visit :url page with :ipAddress IP address from :state state
     */
    public function iVisitPageWithIPAddressFromState(string $url, string $ipAddress, string $state): ElementInterface
    {
        $this->geoIpReader->setCountry('');
        $this->geoIpReader->setState($state);

        return $this->visitPage($url, $ipAddress);
    }

    /**
     * @Given I visit :url page with :ipAddress IP address from :country country
     */
    public function iVisitPageWithIPAddressFromCountry(string $url, string $ipAddress, string $country): ElementInterface
    {
        $this->geoIpReader->setState('');
        $this->geoIpReader->setCountry($country);

        return $this->visitPage($url, $ipAddress);
    }

    private function visitPage(string $url, string $ipAddress): ElementInterface
    {
        return $this->request->send(
            'GET',
            $this->locatePath($url),
            [],
            [],
            null,
            [
                'REMOTE_ADDR' => $ipAddress,
            ]
        );
    }
}
