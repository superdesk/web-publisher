<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Json\Json;
use GuzzleHttp\Client;

class WebhookContext implements Context
{
    /**
     * @Given The payload received by :url webhook should be equal to:
     */
    public function thePayloadReceivedByWebhookShouldBeEqualTo(string $url, PyStringNode $body): void
    {
        $client = new Client();
        $response = $client->request('GET', $url);
        $expected = str_replace('\\"', '"', $body);
        $actual = $response->getBody()->getContents();

        $expected = $this->convertToJson($expected);
        $actual = $this->convertToJson($actual);

        if ($expected !== $actual) {
            throw new \Exception("The body $actual is not equal to $expected");
        }
    }

    private function convertToJson(string $value): string
    {
        try {
            $value = new Json($value);
        } catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        return (string) $value;
    }
}
