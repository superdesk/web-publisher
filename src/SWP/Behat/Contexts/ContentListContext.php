<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class ContentListContext extends AbstractContext implements Context
{
    private $contentListFactory;

    private $contentListRepository;

    private $entityManager;

    public function __construct(FactoryInterface $contentListFactory, ContentListRepositoryInterface $contentListRepository, EntityManagerInterface $entityManager)
    {
        $this->contentListFactory = $contentListFactory;
        $this->contentListRepository = $contentListRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given the following Content Lists:
     */
    public function theFollowingContentLists(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            $contentList = $this->contentListFactory->create();
            $this->fillObject($contentList, $columns);
            $this->entityManager->persist($contentList);
        }

        $this->entityManager->flush();
    }
}
