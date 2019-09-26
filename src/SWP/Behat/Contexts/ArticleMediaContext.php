<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\CoreBundle\Model\ArticleMedia;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class ArticleMediaContext extends AbstractContext implements Context
{
    private $articleMediaFactory;

    private $entityManager;

    private $articleRepository;

    public function __construct(
        FactoryInterface $articleMediaFactory,
        EntityManagerInterface $entityManager,
        ArticleRepositoryInterface $articleRepository
    ) {
        $this->articleMediaFactory = $articleMediaFactory;
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Given the following Article Media:
     */
    public function theFollowingArticleMedia(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            $articleMedia = new ArticleMedia();

            $columns['article'] = $this->articleRepository->findOneBy(['title' => $columns['article']]);

            $this->fillObject($articleMedia, $columns);
            $this->entityManager->persist($articleMedia);
        }

        $this->entityManager->flush();
    }
}
