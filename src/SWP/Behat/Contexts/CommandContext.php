<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class CommandContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var string
     */
    private $commandException;

    /**
     * @var string
     */
    private $commandExceptionMessage;

    /**
     * @var array
     */
    private $options;

    private $eventDispatcher;

    public function __construct(KernelInterface $kernel, EventDispatcherInterface $eventDispatcher)
    {
        $this->kernel = $kernel;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @When I run the :commandName command with options:
     */
    public function iRunTheCommandWithOptions(string $commandName, TableNode $tableNode): void
    {
        $this->application = new Application($this->kernel);
        $this->application->getDefinition()->addOption(new InputOption('--tenant', '-t', InputOption::VALUE_REQUIRED, 'The tenant code'));
        $this->command = $this->application->find($commandName);
        $this->setOptions($tableNode);

        try {
            $this->command->mergeApplicationDefinition();
            $input = new ArrayInput($this->options);
            $input->bind($this->command->getDefinition());
            $this->eventDispatcher->dispatch(ConsoleEvents::COMMAND, new ConsoleCommandEvent($this->command, $input, new ConsoleOutput()));

            $this->getTester($this->command)->execute($this->options);
        } catch (Exception $exception) {
            $path = explode('\\', get_class($exception));
            $this->commandException = array_pop($path);
            $this->commandExceptionMessage = $exception->getMessage();
        }
    }

    /**
     * @param string $expectedOutput
     *
     * @Then /^the command output should be "([^"]*)"$/
     */
    public function theCommandOutputShouldBe($expectedOutput): void
    {
        if ($this->commandTester) {
            $current = trim($this->commandTester->getDisplay());
            if (false === strpos($current, $expectedOutput)) {
                throw new LogicException(sprintf('Current output is: [%s]', $current));
            }

            return;
        }

        throw new LogicException(sprintf('Command wasn\'t executed. Exception: [%s]', $this->commandExceptionMessage));
    }

    /**
     * @Then /^the command exception should be "([^"]*)"$/
     */
    public function theCommandExceptionShouldBe(string $expectedException)
    {
        if (null === $this->commandException) {
            throw new LogicException('Exception was not registered');
        }

        if ($this->commandException != $expectedException) {
            throw new LogicException(sprintf('Current exception is: [%s]', $this->commandException));
        }
    }

    /**
     * @Then /^the command exception message should be "([^"]*)"$/
     */
    public function theCommandExceptionMessageShouldBe(string $result)
    {
        if (false === strpos($this->commandExceptionMessage, $result)) {
            throw new Exception(sprintf('Could not see "%s" in exception message "%s"', $result, $this->commandExceptionMessage));
        }
    }

    private function setOptions(TableNode $tableNode)
    {
        $options = [];
        foreach ($tableNode->getRowsHash() as $key => $value) {
            $options[$key] = '' === $value ? null : $value;
        }

        $this->options = $options;
    }

    private function getTester(Command $command)
    {
        $this->commandTester = new CommandTester($command);

        return $this->commandTester;
    }
}
