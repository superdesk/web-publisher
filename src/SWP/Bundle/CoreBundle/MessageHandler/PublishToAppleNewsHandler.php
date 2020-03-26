<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\MessageHandler;

use SWP\Bundle\CoreBundle\AppleNews\AppleNewsPublisher;
use SWP\Bundle\CoreBundle\MessageHandler\Message\PublishToAppleNews;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PublishToAppleNewsHandler implements MessageHandlerInterface
{
    private $appleNewsPublisher;

    public function __construct(AppleNewsPublisher $appleNewsPublisher)
    {
        $this->appleNewsPublisher = $appleNewsPublisher;
    }

    public function __invoke(PublishToAppleNews $publishToAppleNews)
    {
        $articleId = $publishToAppleNews->getArticleId();
        $tenantId = $publishToAppleNews->getTenantId();

        $this->appleNewsPublisher->publish();

        //$this->article->flush();
    }
}
