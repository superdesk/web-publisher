<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProcessPackageRulesSubscriber implements EventSubscriberInterface
{
    /**
     * @var RuleProcessorInterface
     */
    private $ruleProcessor;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ProcessArticleRulesSubscriber constructor.
     *
     * @param RuleProcessorInterface $ruleProcessor
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RuleProcessorInterface $ruleProcessor,
        EntityManagerInterface $entityManager
    ) {
        $this->ruleProcessor = $ruleProcessor;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PACKAGE_POST_CREATE => 'processRules',
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function processRules(GenericEvent $event)
    {
        $this->entityManager->getFilters()->disable('tenantable');
        $this->ruleProcessor->process($event->getSubject());
        $this->entityManager->getFilters()->enable('tenantable');
    }
}
