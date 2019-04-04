<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
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
        $actual = $this->normalizeJson($response->getBody()->getContents());
        $expected = $this->normalizeJson($body->getRaw());

        if ($actual !== $expected) {
            throw new \Exception("The actual body: \n $actual \n\n is not equal to expected: \n $expected");
        }
    }

    private function normalizeJson(string $value): string
    {
        $value = \json_decode($value, true);
        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new \Exception("The string '$value' is not valid json");
        }

        return \json_encode($value);
    }
}
