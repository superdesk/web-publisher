<?php

declare(strict_types=1);

namespace SWP\Behat\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FixturesHookListener implements EventSubscriberInterface
{
    /**
     * @var string feature|scenario
     */
    private $lifetime;

    /**
     * @var object
     */
    private $fixtureService;

    /**
     * Constructor.
     *
     * @param string $lifetime
     */
    public function __construct($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ExerciseCompleted::BEFORE => 'beforeExercise',
            FeatureTested::BEFORE => 'beforeFeature',
            FeatureTested::AFTER => 'afterFeature',
            ExampleTested::BEFORE => 'beforeScenario',
            ScenarioTested::BEFORE => 'beforeScenario',
            ExampleTested::AFTER => 'afterScenario',
            ScenarioTested::AFTER => 'afterScenario',
        ];
    }

    /**
     * Set fixture service.
     *
     * @param \BehatExtension\DoctrineDataFixturesExtension\Service\FixtureService $service
     */
    public function setFixtureService($service)
    {
        $this->fixtureService = $service;
    }

    /**
     * Listens to "exercise.before" event.
     *
     * @param \Behat\Testwork\Tester\Event\ExerciseCompleted $event
     */
    public function beforeExercise(ExerciseCompleted $event)
    {
        $this->fixtureService
            ->cacheFixtures();
    }

    /**
     * Listens to "feature.before" event.
     *
     * @param \Behat\Behat\Tester\Event\FeatureTested $event
     */
    public function beforeFeature(FeatureTested $event)
    {
        if ('feature' !== $this->lifetime) {
            return;
        }

        $this->fixtureService->reloadFixtures(\in_array('disable-fixtures', $event->getFeature()->getTags()));
    }

    /**
     * Listens to "feature.after" event.
     *
     * @param \Behat\Behat\Tester\Event\FeatureTested $event
     */
    public function afterFeature(FeatureTested $event)
    {
        if ('feature' !== $this->lifetime) {
            return;
        }

        $this->fixtureService
            ->flush();
    }

    /**
     * Listens to "scenario.before" and "outline.example.before" event.
     *
     * @param \Behat\Behat\Tester\Event\AbstractScenarioTested $event
     */
    public function beforeScenario(ScenarioTested $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        $this->fixtureService
            ->reloadFixtures();
    }

    /**
     * Listens to "scenario.after" and "outline.example.after" event.
     *
     * @param \Behat\Behat\Tester\Event\AbstractScenarioTested $event
     */
    public function afterScenario(ScenarioTested $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        $this->fixtureService
            ->flush();
    }
}
