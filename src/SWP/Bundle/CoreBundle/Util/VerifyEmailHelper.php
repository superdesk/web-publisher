<?php

/*
 * This file is part of the SymfonyCasts VerifyEmailBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SWP\Bundle\CoreBundle\Util;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\InvalidSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\WrongEmailVerifyException;
use SymfonyCasts\Bundle\VerifyEmail\Generator\VerifyEmailTokenGenerator;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\Util\VerifyEmailQueryUtility;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
final class VerifyEmailHelper implements VerifyEmailHelperInterface
{
    private $router;
    private $uriSigner;
    private $queryUtility;
    private $tokenGenerator;

    /**
     * @var int The length of time in seconds that a signed URI is valid for after it is created
     */
    private $lifetime;

    private TenantContextInterface $tenantContext;

    public function __construct(UrlGeneratorInterface $router,
                                UriSigner $uriSigner,
                                VerifyEmailQueryUtility $queryUtility,
                                VerifyEmailTokenGenerator $generator,
                                int $lifetime,
                                TenantContextInterface $tenantContext
    ) {
        $this->router = $router;
        $this->uriSigner = $uriSigner;
        $this->queryUtility = $queryUtility;
        $this->tokenGenerator = $generator;
        $this->lifetime = $lifetime;

        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSignature(string $routeName, string $userId, string $userEmail, array $extraParams = []): VerifyEmailSignatureComponents
    {
        $generatedAt = time();
        $expiryTimestamp = $generatedAt + $this->lifetime;

        $extraParams['token'] = $this->tokenGenerator->createToken($userId, $userEmail);
        $extraParams['expires'] = $expiryTimestamp;

        $uri = $this->router->generate($routeName, $extraParams, UrlGeneratorInterface::ABSOLUTE_URL);

        $signature = $this->uriSigner->sign($uri);
        $signature = $this->applyPWAUrl($signature);

        /* @psalm-suppress PossiblyFalseArgument */
        return new VerifyEmailSignatureComponents(\DateTimeImmutable::createFromFormat('U', (string) $expiryTimestamp), $signature, $generatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function validateEmailConfirmation(string $signedUrl, string $userId, string $userEmail): void
    {
        if (!$this->uriSigner->check($signedUrl)) {
            throw new InvalidSignatureException();
        }

        if ($this->queryUtility->getExpiryTimestamp($signedUrl) <= time()) {
            throw new ExpiredSignatureException();
        }

        $knownToken = $this->tokenGenerator->createToken($userId, $userEmail);
        $userToken = $this->queryUtility->getTokenFromQuery($signedUrl);

        if (!hash_equals($knownToken, $userToken)) {
            throw new WrongEmailVerifyException();
        }
    }

    private function applyPWAUrl(string $url): string
    {
        $tenant = $this->tenantContext->getTenant();
        if ($tenant &&
            $tenant->getPWAConfig() &&
            $tenant->getPWAConfig()->getUrl()
        ) {
            $PWAUrlParts = parse_url($tenant->getPWAConfig()->getUrl());
            $urlParts = parse_url($url);
            $scheme = $PWAUrlParts['scheme'] ?? 'https';
            $scheme .= '://';
            $host = $PWAUrlParts['host']; //. ':';
            $port = isset($PWAUrlParts['port']) ? ':'.$PWAUrlParts['port'] : '';
            $query = isset($urlParts['query']) ? '?'.$urlParts['query'] : '';
            $url = $scheme.$host.$port.$urlParts['path'].$query;
        }

        return $url;
    }
}
