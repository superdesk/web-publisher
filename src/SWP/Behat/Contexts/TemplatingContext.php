<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use SWP\Bundle\ContentBundle\Loader\ArticleLoader;

final class TemplatingContext implements Context
{
    private $templating;

    private $articleLoader;

    /**
     * @var string
     */
    private $lastRenderedContent;

    public function __construct(\Twig_Environment $templating, ArticleLoader $articleLoader)
    {
        $this->templating = $templating;
        $this->articleLoader = $articleLoader;
    }

    /**
     * @Given I set :slug as a current article in the context
     */
    public function IsetAsACurrentArticleInTheContext(string $slug): void
    {
        $this->articleLoader->load('article', ['slug' => $slug]);
    }

    /**
     * @Given I render a template with content:
     */
    public function iRenderATemplateWithContent(PyStringNode $templateContent): void
    {
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
}
