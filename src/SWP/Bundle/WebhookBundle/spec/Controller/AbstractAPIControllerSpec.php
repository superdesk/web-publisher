<?php

namespace spec\SWP\Bundle\WebhookBundle\Controller;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\WebhookBundle\Controller\AbstractAPIController;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class AbstractAPIControllerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('spec\SWP\Bundle\WebhookBundle\Controller\AbstractApiControllerImplementation');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\WebhookBundle\Controller\AbstractAPIController');
    }

    public function it_lists_webhooks(
        RepositoryInterface $repository,
        EventDispatcherInterface $eventDispatcher,
        \Countable $pagination
    ) {
        $request = new Request();
        $repository->getPaginatedByCriteria(Argument::type(EventDispatcherInterface::class),Argument::type(Criteria::class), Argument::type('array'), Argument::type(PaginationData::class))->shouldBeCalled()->willReturn($pagination);
        $this->listWebhooks($eventDispatcher, $repository, $request)->shouldBeAnInstanceOf(ResourcesListResponse::class);
    }

    public function it_get_single_webhook(WebhookInterface $webhook)
    {
        $this->getSingleWebhook($webhook)->shouldReturnAnInstanceOf(SingleResourceResponse::class);
    }

    public function it_creates_new_webhook(
        RepositoryInterface $repository,
        FactoryInterface $factory,
        FormFactoryInterface $formFactory,
        Form $form
    ) {
        $request = new Request();
        $formFactory->createNamed(Argument::cetera())->willReturn($form);
        $this->createWebhook($repository, $factory, $request, $formFactory)->shouldReturnAnInstanceOf(SingleResourceResponse::class);
    }

    public function it_updates_webhook(ObjectManager $objectManager, WebhookInterface $webhook, FormFactoryInterface $formFactory, Form $form)
    {
        $request = new Request();
        $formFactory->createNamed(Argument::cetera())->willReturn($form);
        $this->updateWebhook($objectManager, $request, $webhook, $formFactory)->shouldReturnAnInstanceOf(SingleResourceResponse::class);
    }

    public function it_deletes_webhook(RepositoryInterface $repository, WebhookInterface $webhook)
    {
        $this->deleteWebhook($repository, $webhook)->shouldReturnAnInstanceOf(SingleResourceResponse::class);
    }
}

class AbstractApiControllerImplementation extends AbstractAPIController
{
}
