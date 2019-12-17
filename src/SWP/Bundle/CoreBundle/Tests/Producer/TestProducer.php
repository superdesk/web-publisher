<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Producer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;

final class TestProducer implements ProducerInterface
{
    private $consumer;

    public function __construct(ConsumerInterface $consumer)
    {
        $this->consumer = $consumer;
    }

    public function publish($msgBody, $routingKey = '', $additionalProperties = [])
    {
        $this->consumer->execute(new AMQPMessage($msgBody, $additionalProperties));
    }
}
