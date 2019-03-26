<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class ContentListItemContext extends AbstractContext implements Context
{
    private $contentListItemFactory;

    private $contentListRepository;

    private $articleRepository;

    public function __construct(
        FactoryInterface $contentListItemFactory,
        ContentListRepositoryInterface $contentListRepository,
        ArticleRepositoryInterface $articleRepository
    ) {
        $this->contentListItemFactory = $contentListItemFactory;
        $this->contentListRepository = $contentListRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Given the following Content List Items:
     */
    public function theFollowingContentListItems(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            /** @var ContentListItemInterface $contentListItem */
            $contentListItem = $this->contentListItemFactory->create();
            $contentListItem->setPosition(0);

            $columns['content_list'] = $this->contentListRepository->findOneBy(['name' => $columns['content_list']]);
            $columns['content'] = $this->articleRepository->findOneBy(['title' => $columns['article']]);
            unset($columns['article']);

            $this->fillObject($contentListItem, $columns);
            $this->contentListRepository->persist($contentListItem);
            $this->contentListRepository->flush();
        }
    }
}
