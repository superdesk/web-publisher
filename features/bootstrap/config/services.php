<?php

declare(strict_types=1);

// features/bootstrap/config/services.php

$container->setParameter('behat.doctrine_data_fixtures.service.hook_listener.class', \SWP\Behat\Listener\FixturesHookListener::class);
$container->setParameter('behat.doctrine_data_fixtures.service.fixture_loader.class', \SWP\Behat\Service\FixtureService::class);
