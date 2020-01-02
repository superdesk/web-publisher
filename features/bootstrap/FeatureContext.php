<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext, MinkAwareContext
{
    private $output = '';

    private $exception = '';

    /**
     * @When I run :command command
     */
    public function iRunCommand($command)
    {
        exec(sprintf('php bin/console %s --env=test 2>&1', $command), $this->output, $returnVar);

        if (0 !== $returnVar) {
            $this->exception = implode("\n", $this->output);
        } else {
            $this->output = implode("\n", $this->output);
        }
    }

    /**
     * @When I run :command command for :number seconds
     */
    public function iRunCommandForXSeconds($command, $number)
    {
        try {
            $this->output = self::exec_timeout(sprintf('php bin/console %s --env=test 2>&1', $command), $number);
            $this->output = implode("\n", $this->output);
        } catch (\Exception $e) {
            $this->exception = $e->getMessage();
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

    /**
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowTheRedirection()
    {
        $client = $this->getMink()->getSession()->getDriver()->getClient();
        $client->followRedirects(true);
        $client->followRedirect();
    }

    /**
     * @Then (I )wait :count second(s)
     */
    public function iWaitSeconds($count)
    {
        usleep($count * 1000000);
    }

    /**
     * Execute a command and return it's output. Either wait until the command exits or the timeout has expired.
     *
     * @param string $cmd     command to execute
     * @param number $timeout timeout in seconds
     *
     * @return string output of the command
     *
     * @throws \Exception
     */
    private static function exec_timeout($cmd, $timeout)
    {
        // File descriptors passed to the process.
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],   // stderr
        ];

        // Start the process.
        $process = proc_open('exec '.$cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \Exception('Could not execute process');
        }

        // Set the stdout stream to none-blocking.
        stream_set_blocking($pipes[1], false);

        // Turn the timeout into microseconds.
        $timeout = $timeout * 1000000;

        // Output buffer.
        $buffer = '';

        // While we have time to wait.
        while ($timeout > 0) {
            $start = microtime(true);

            // Wait until we have output or the timer expired.
            $read = [$pipes[1]];
            $other = [];
            stream_select($read, $other, $other, 0, (int) $timeout);

            // Get the status of the process.
            // Do this before we read from the stream,
            // this way we can't lose the last bit of output if the process dies between these functions.
            $status = proc_get_status($process);

            // Read the contents from the buffer.
            // This function will always return immediately as the stream is none-blocking.
            $buffer .= stream_get_contents($pipes[1]);

            if (!$status['running']) {
                // Break from this loop if the process exited before the timeout.
                break;
            }

            // Subtract the number of microseconds that we waited.
            $timeout -= (microtime(true) - $start) * 1000000;
        }

        // Check if there were any errors.
        $errors = stream_get_contents($pipes[2]);

        if (!empty($errors)) {
            throw new \Exception($errors);
        }

        // Kill the process in case the timeout expired and it's still running.
        // If the process already exited this won't do anything.
        proc_terminate($process, 9);

        // Close all streams.
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);

        return $buffer;
    }
}
