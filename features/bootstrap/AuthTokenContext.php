<?php

declare(strict_types=1);

use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use SWP\Bundle\CoreBundle\Factory\ApiKeyFactory;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepositoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class AuthTokenContext extends RestContext
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var ApiKeyRepositoryInterface
     */
    private $apiKeyRepository;

    /**
     * @var ApiKeyFactory
     */
    private $apiKeyFactory;

    /**
     * AuthTokenContext constructor.
     */
    public function __construct(Request $request, UserProviderInterface $userProvider, ApiKeyRepositoryInterface $apiKeyRepository, ApiKeyFactory $apiKeyFactory)
    {
        parent::__construct($request);
        $this->userProvider = $userProvider;
        $this->apiKeyRepository = $apiKeyRepository;
        $this->apiKeyFactory = $apiKeyFactory;
    }

    /**
     * @Given I am authenticated as ":username"
     */
    public function iAmAuthenticatedAs(string $username)
    {
        $user = $this->userProvider->loadUserByUsername($username);
        /** @var \SWP\Bundle\CoreBundle\Model\ApiKeyInterface $apiKey */
        $apiKey = $this->apiKeyFactory->create($user);
        $this->apiKeyRepository->add($apiKey);
        $this->request->setHttpHeader('Authorization', 'Basic '.$apiKey->getApiKey());
    }

    /**
     * @BeforeScenario
     */
    public function restoreAuthHeader()
    {
        $this->request->setHttpHeader('Authorization', '');
    }

    /**
     * @When /^I grab the confirmation url and follow it$/
     */
    public function iGrabTheConfirmationUrlAndFollowIt()
    {
        $url = json_decode($this->request->getContent())->url;
        $this->iSendARequestTo('GET', $url);
    }

    /**
     * @When /^I grab the confirmation url convert it to publisher domain and follow it$/
     */
    public function iGrabTheConfirmationUrlConvertItToPublisherDomainAndFollowIt()
    {
        $url = str_replace('http://pwa_url.local:80/', $this->getMinkParameter('base_url'), json_decode($this->request->getContent())->url);
        $this->iSendARequestTo('GET', $url);
    }
}
