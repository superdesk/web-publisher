<?php

use Behatch\Context\BaseContext;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;

class GeoLocationContext extends BaseContext
{
    /** @var Request  */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @Given I visit :url page with :ipAddress IP address
     */
    public function iVisitPageWithIPAddress(string $url, string $ipAddress)
    {
        return $this->request->send(
            'GET',
            $this->locatePath($url),
            [],
            [],
            null,
            [
                'REMOTE_ADDR' => $ipAddress
            ]
        );
    }
}
