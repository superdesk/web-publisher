<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\SlideshowItemInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class SlideshowItemContext extends AbstractContext implements Context
{
    private $slideshowItemFactory;

    private $slideshowRepository;

    private $articleMediaRepository;

    public function __construct(
        FactoryInterface $slideshowItemFactory,
        SlideshowRepositoryInterface $slideshowRepository,
        ArticleMediaRepositoryInterface $articleRepository
    ) {
        $this->slideshowItemFactory = $slideshowItemFactory;
        $this->slideshowRepository = $slideshowRepository;
        $this->articleMediaRepository = $articleRepository;
    }

    /**
     * @Given the following Slideshow Items:
     */
    public function theFollowingSlideshowItems(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            /** @var SlideshowItemInterface $slideshowItem */
            $slideshowItem = $this->slideshowItemFactory->create();
            $slideshow = $this->slideshowRepository->findOneBy(['code' => $columns['slideshow']]);
            $columns['article_media'] = $this->articleMediaRepository->findOneBy(['key' => $columns['media']]);
            unset($columns['slideshow'], $columns['media']);

            $this->fillObject($slideshowItem, $columns);

            $slideshow->addItem($slideshowItem);
        }

        $this->slideshowRepository->flush();
    }
}
