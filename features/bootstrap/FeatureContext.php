<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $output = '';

    /**
     * @When I run :command command
     */
    public function iRunCommand($command)
    {
        $this->output = shell_exec(sprintf('php app/console %s', $command));
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
}
