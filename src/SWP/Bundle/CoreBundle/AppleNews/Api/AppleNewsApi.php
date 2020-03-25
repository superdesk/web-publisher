<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Api;

use GuzzleHttp\Client;
use SWP\Bundle\CoreBundle\AppleNews\Api\Response\AppleNewsArticle;

final class AppleNewsApi
{
    private $httpClient;

    private $apiKeyId;

    private $apiKeySecret;

    public function __construct(Client $httpClient, string $apiKeyId, string $apiKeySecret)
    {
        $this->httpClient = $httpClient;
        $this->apiKeyId = $apiKeyId;
        $this->apiKeySecret = $apiKeySecret;
    }

    public function createArticle(string $channelId, string $json, array $metadata = []): AppleNewsArticle
    {
        $path = "/channels/$channelId/articles";
        $response = $this->httpClient->post($path, [
            'multipart' => $this->generateData($json, $metadata),
            'headers' => [
                'Authorization' => $this->getAuthorization('POST', $path, ''),
            ],
        ]);

        $jsonResponse = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return AppleNewsArticle::fromRawResponse($jsonResponse);
    }

    public function updateArticle(string $channelId, string $articleId, string $json, array $metadata = []): AppleNewsArticle
    {
        $path = "/channels/$channelId/articles/{$articleId}";
        $response = $this->httpClient->post($path, [
            'multipart' => $this->generateData($json, $metadata),
            'headers' => [
                'Authorization' => $this->getAuthorization('POST', $path, ''),
            ],
        ]);

        $jsonResponse = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return AppleNewsArticle::fromRawResponse($jsonResponse);
    }

    private function generateData(string $json, array $metadata): array
    {
        $data = [
            [
                'name' => 'article',
                'filename' => 'article.json',
                'contents' => $json,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        ];

        if (!empty($metadata)) {
            $data[] = [
                'name' => 'metadata',
                'contents' => json_encode(['data' => $metadata], JSON_THROW_ON_ERROR, 512),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ];
        }

        return $data;
    }

    private function generateHhmacSignature(string $message): string
    {
        $keyBytes = base64_decode($this->apiKeySecret);
        $hashed = hash_hmac('sha256', $message, $keyBytes, true);

        return base64_encode($hashed);
    }

    private function getAuthorization(string $method, string $path, string $content = ''): string
    {
        $date = gmdate(\DateTime::ATOM);
        $canonical = $method.$path.$date.$content;
        $signature = $this->generateHhmacSignature($canonical, $this->apiKeySecret);

        return "HHMAC; key=$this->apiKeyId; signature=$signature; date=$date";
    }
}
