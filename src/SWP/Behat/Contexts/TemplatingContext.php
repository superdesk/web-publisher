<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Loader\ArticleLoader;
use SWP\Bundle\ContentBundle\Twig\Cache\CacheBlockTagsCollectorInterface;
use Twig\Environment;

final class TemplatingContext implements Context
{
    private $templating;

    private $articleLoader;

    private $lastRenderedContent;

    private $cacheBlockTagsCollector;

    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        Environment $templating,
        ArticleLoader $articleLoader,
        CacheBlockTagsCollectorInterface $cacheBlockTagsCollector
    ) {
        $this->templating = $templating;
        $this->articleLoader = $articleLoader;
        $this->cacheBlockTagsCollector = $cacheBlockTagsCollector;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given I set :slug as a current article in the context
     */
    public function ISetAsACurrentArticleInTheContext(string $slug): void
    {
        $this->articleLoader->load('article', ['slug' => $slug]);
    }

    /**
     * @Given I render a template with content:
     */
    public function iRenderATemplateWithContent(PyStringNode $templateContent): void
    {
        $this->entityManager->clear();
        $template = $this->templating->createTemplate($templateContent->getRaw());
        $this->lastRenderedContent = $template->render();
    }

    /**
     * @Then rendered template should contain :searchString
     */
    public function renderedTemplateShouldContain(string $searchString): void
    {
        if (false === \strpos($this->lastRenderedContent, $searchString)) {
            throw new \Exception('Searched string was not found in rendered template.');
        }
    }

    /**
     * @Then rendered template should not contain :searchString
     */
    public function renderedTemplateShouldNotContain(string $searchString)
    {
        if (false !== \strpos($this->lastRenderedContent, $searchString)) {
            throw new \Exception('Searched string was found in rendered template (and was not expected).');
        }
    }

    /**
     * @Then rendered template should be equal to:
     */
    public function renderedTemplateShouldBeEqualTo(PyStringNode $templateContent): void
    {
        if ($this->lastRenderedContent !== $templateContent->getRaw()) {
            throw new \Exception('The content is not equal!');
        }
    }

    /**
     * @Then CacheBlockTagsCollector should have tag :tagName
     */
    public function cacheblocktagscollectorShouldHaveTag(string $tagName)
    {
        if (!in_array($tagName, $this->cacheBlockTagsCollector->getCurrentCacheBlockTags())) {
            throw new \Exception(sprintf('Tag %s was not found. Found tags: %s', $tagName, implode(', ', $this->cacheBlockTagsCollector->getCurrentCacheBlockTags())));
        }
    }
}
