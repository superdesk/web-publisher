<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $output = '';

    private $exception = '';

    /**
     * @When I run :command command
     */
    public function iRunCommand($command)
    {
        exec(sprintf('php app/console %s --env=test 2>&1', $command), $this->output, $returnVar);

        if (0 !== $returnVar) {
            $this->exception = implode("\n", $this->output);
        } else {
            $this->output = implode("\n", $this->output);
        }
    }

    /**
     * @Then I should see :result in the output
     */
    public function iShouldSeeInTheOutput(string $result)
    {
        if (false === strpos($this->output, $result)) {
            throw new \Exception(sprintf('Could not see "%s" in output "%s"', $result, $this->output));
        }
    }

    /**
     * @Then I should see :result in the exception
     */
    public function iShouldSeeInTheException(string $result)
    {
        if (false === strpos($this->exception, $result)) {
            throw new \Exception(sprintf('Could not see "%s" in exception "%s"', $result, $this->exception->getMessage()));
        }
    }
}
