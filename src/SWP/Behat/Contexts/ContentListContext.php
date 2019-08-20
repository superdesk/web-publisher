<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use function json_decode;

final class ContentListContext extends AbstractContext implements Context
{
    private $contentListFactory;

    private $entityManager;

    public function __construct(FactoryInterface $contentListFactory, EntityManagerInterface $entityManager)
    {
        $this->contentListFactory = $contentListFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given the following Content Lists:
     */
    public function theFollowingContentLists(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            $contentList = $this->contentListFactory->create();
            if (isset($columns['filters'])) {
                $columns['filters'] = json_decode($columns['filters'], true);
            }
            $this->fillObject($contentList, $columns);
            $this->entityManager->persist($contentList);
        }

        $this->entityManager->flush();
    }
}
