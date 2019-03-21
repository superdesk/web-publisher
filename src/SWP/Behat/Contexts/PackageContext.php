<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;
use SWP\Component\Bridge\Events;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PackageContext extends AbstractContext implements Context
{
    private $tenantContext;

    private $jsonToPackageTransformer;

    private $eventDispatcher;

    private $contentPushProducer;

    public function __construct(
        TenantContextInterface $tenantContext,
        JsonToPackageTransformer $jsonToPackageTransformer,
        EventDispatcherInterface $eventDispatcher,
        ProducerInterface $contentPushProducer
    ) {
        $this->tenantContext = $tenantContext;
        $this->jsonToPackageTransformer = $jsonToPackageTransformer;
        $this->eventDispatcher = $eventDispatcher;
        $this->contentPushProducer = $contentPushProducer;
    }

    /**
     * @Given the following Package ninjs:
     */
    public function theFollowingPackageNinjs(PyStringNode $node)
    {
        $package = $this->jsonToPackageTransformer->transform($node->getRaw());
        $this->eventDispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($package));

        $payload = \serialize([
            'package' => $package,
            'tenant' => $this->tenantContext->getTenant(),
        ]);

        $this->contentPushProducer->publish($payload);
    }
}
