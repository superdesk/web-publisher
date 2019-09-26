<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class SlideshowContext extends AbstractContext implements Context
{
    private $slideshowFactory;

    private $entityManager;

    private $articleRepository;

    public function __construct(
        FactoryInterface $slideshowFactory,
        EntityManagerInterface $entityManager,
        ArticleRepositoryInterface $articleRepository
    ) {
        $this->slideshowFactory = $slideshowFactory;
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Given the following Slideshows:
     */
    public function theFollowingContentLists(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            $slideshow = $this->slideshowFactory->create();

            $columns['article'] = $this->articleRepository->findOneBy(['title' => $columns['article']]);

            $this->fillObject($slideshow, $columns);
            $this->entityManager->persist($slideshow);
        }

        $this->entityManager->flush();
    }
}
