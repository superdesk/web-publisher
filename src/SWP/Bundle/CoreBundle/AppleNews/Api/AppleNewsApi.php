<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use SWP\Bundle\CoreBundle\AppleNews\Api\Response\AppleNewsArticle;

final class AppleNewsApi
{
    private $httpClient;

    private $apiKeyId;

    private $apiKeySecret;

    private $boundary;

    public function __construct(Client $httpClient, string $apiKeyId, string $apiKeySecret)
    {
        $this->httpClient = $httpClient;
        $this->apiKeyId = $apiKeyId;
        $this->apiKeySecret = $apiKeySecret;
        $this->boundary = substr(md5(microtime()), random_int(0, 26), 64);
    }

    public function createArticle(string $channelId, string $json, array $metadata = []): AppleNewsArticle
    {
        $path = "/channels/$channelId/articles";

        $multipartStream = new MultipartStream($this->generateData($json, $metadata), $this->boundary);

        $response = $this->httpClient->post($path, [
            'body' => $multipartStream,
            'headers' => [
                'Authorization' => $this->getAuthorizationSignature('POST', $path, $this->getContentType().$multipartStream->getContents()),
                'Content-Type' => $this->getContentType(),
                'Content-Length' => strlen($json),
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
                'Authorization' => $this->getAuthorizationSignature('POST', $path, ''),
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

    private function getAuthorizationSignature(string $method, string $path, string $content = ''): string
    {
        $date = gmdate(\DateTime::ATOM);
        $url = ClientFactory::BASE_URI.$path;
        $canonical = $method.$url.$date.$content;

        $signature = $this->generateHhmacSignature($canonical, $this->apiKeySecret);

        return 'HHMAC; key="'.$this->apiKeyId.'"; signature="'.$signature.'"; date="'.$date.'"';
    }

    private function getContentType(): string
    {
        return 'multipart/form-data; boundary='.$this->boundary;
    }
}
