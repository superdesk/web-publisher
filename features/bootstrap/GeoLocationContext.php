<?php

use Behat\Mink\Element\ElementInterface;
use Behatch\Context\BaseContext;
use Behatch\HttpCall\Request;

class GeoLocationContext extends BaseContext
{
    /** @var Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @Given I visit :url page with :ipAddress IP address
     */
    public function iVisitPageWithIPAddress(string $url, string $ipAddress): ElementInterface
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
