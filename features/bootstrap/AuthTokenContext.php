<?php

declare(strict_types=1);

use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepositoryInterface;
use SWP\Bundle\CoreBundle\Factory\ApiKeyFactory;

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
     *
     * @param Request                   $request
     * @param UserProviderInterface     $userProvider
     * @param ApiKeyRepositoryInterface $apiKeyRepository
     * @param ApiKeyFactory             $apiKeyFactory
     */
    public function __construct(Request $request, UserProviderInterface $userProvider, ApiKeyRepositoryInterface $apiKeyRepository, ApiKeyFactory $apiKeyFactory)
    {
        parent::__construct($request);
        $this->userProvider = $userProvider;
        $this->apiKeyRepository = $apiKeyRepository;
        $this->apiKeyFactory = $apiKeyFactory;
    }

    /**
     * @param string $username
     *
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
        $this->request->setHttpHeader('Authorization', null);
    }

    /**
     * @When /^I grab the confirmation url and follow it$/
     */
    public function iGrabTheConfirmationUrlAndFollowIt()
    {
        $url = json_decode($this->request->getContent())->url;
        $this->url = $url;
        $request = $this->iSendARequestTo("GET", $url);
    }
}
