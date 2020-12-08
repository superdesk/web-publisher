<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\CoreBundle\Factory\ApiKeyFactory;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepositoryInterface;

final class UserContext extends AbstractContext implements Context
{
    private $userManager;

    private $apiKeyFactory;

    private $apiKeyRepository;

    public function __construct(UserManagerInterface $userManager, ApiKeyFactory $apiKeyFactory, ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->userManager = $userManager;
        $this->apiKeyFactory = $apiKeyFactory;
        $this->apiKeyRepository = $apiKeyRepository;
    }

    /**
     * @Given the following Users:
     */
    public function theFollowingUsers(TableNode $table): void
    {
        foreach ($table as $row => $columns) {
            if (isset($columns['email']) && null !== $this->userManager->findUserByEmail($columns['email'])) {
                continue;
            }

            /** @var UserInterface $user */
            $user = $this->userManager->createUser();

            if (isset($columns['role'])) {
                $user->addRole($columns['role']);
                unset($columns['role']);
            }

            $token = null;
            if (isset($columns['token'])) {
                $token = $columns['token'];
                unset($columns['token']);
            }

            $this->fillObject($user, $columns);
            $this->userManager->updateUser($user);

            $apiKey = $this->apiKeyFactory->create($user, base64_encode($token));

            $this->apiKeyRepository->add($apiKey);
            $this->apiKeyRepository->add($user);
        }
    }
}
