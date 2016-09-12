<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplyRulesToArticleSubscriber implements EventSubscriberInterface
{
    /**
     * @var RuleProcessorInterface
     */
    private $ruleProcessor;

    /**
     * ApplyRulesToArticleSubscriber constructor.
     *
     * @param RuleProcessorInterface $ruleProcessor
     */
    public function __construct(RuleProcessorInterface $ruleProcessor)
    {
        $this->ruleProcessor = $ruleProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::PRE_CREATE => 'applyRules',
        ];
    }

    /**
     * @param ArticleEvent $event
     */
    public function applyRules(ArticleEvent $event)
    {
        $this->ruleProcessor->process($event->getArticle());
    }
}
