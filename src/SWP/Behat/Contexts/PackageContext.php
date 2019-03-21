<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType;
use SWP\Bundle\CoreBundle\Model\CompositePublishAction;
use SWP\Bundle\CoreBundle\Repository\PackageRepositoryInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;
use SWP\Component\Bridge\Events;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class PackageContext extends AbstractContext implements Context
{
    private $tenantContext;

    private $jsonToPackageTransformer;

    private $eventDispatcher;

    private $contentPushProducer;

    private $articlePublisher;

    private $formFactory;

    private $packageRepository;

    public function __construct(
        TenantContextInterface $tenantContext,
        JsonToPackageTransformer $jsonToPackageTransformer,
        EventDispatcherInterface $eventDispatcher,
        ProducerInterface $contentPushProducer,
        ArticlePublisherInterface $articlePublisher,
        FormFactoryInterface $formFactory,
        PackageRepositoryInterface $packageRepository
    ) {
        $this->tenantContext = $tenantContext;
        $this->jsonToPackageTransformer = $jsonToPackageTransformer;
        $this->eventDispatcher = $eventDispatcher;
        $this->contentPushProducer = $contentPushProducer;
        $this->articlePublisher = $articlePublisher;
        $this->formFactory = $formFactory;
        $this->packageRepository = $packageRepository;
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

    /**
     * @Given I publish the submitted package :guid:
     */
    public function iPublishTheSubmittedPackage(string $guid, PyStringNode $string): void
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $package = $this->packageRepository->findOneBy(['guid' => $guid]);

        if (null === $package) {
            throw new \Exception('Package not found');
        }

        $form = $this->submitForm($string);

        if ($form->isValid()) {
            $this->articlePublisher->publish($package, $form->getData());
        } else {
            throw new \Exception('Invalid form data');
        }
    }

    private function submitForm(PyStringNode $string): FormInterface
    {
        $form = $this->formFactory->create(CompositePublishActionType::class, new CompositePublishAction(), []);
        $form->submit(\json_decode($string->getRaw(), true), true);

        return $form;
    }
}
