<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMessage;
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
use Symfony\Component\Messenger\MessageBusInterface;

final class PackageContext extends AbstractContext implements Context
{
    private $tenantContext;

    private $jsonToPackageTransformer;

    private $eventDispatcher;

    private $messageBus;

    private $articlePublisher;

    private $formFactory;

    private $packageRepository;

    public function __construct(
        TenantContextInterface $tenantContext,
        JsonToPackageTransformer $jsonToPackageTransformer,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $messageBus,
        ArticlePublisherInterface $articlePublisher,
        FormFactoryInterface $formFactory,
        PackageRepositoryInterface $packageRepository
    ) {
        $this->tenantContext = $tenantContext;
        $this->jsonToPackageTransformer = $jsonToPackageTransformer;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageBus = $messageBus;
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
        $this->messageBus->disptach(new ContentPushMessage($this->tenantContext->getTenant()->getId(), $node->getRaw()));
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

        if ($form->isSubmitted() && $form->isValid()) {
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
