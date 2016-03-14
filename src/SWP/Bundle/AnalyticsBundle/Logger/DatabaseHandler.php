<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\AnalyticsBundle\Logger;

use SWP\AnalyticsBundle\Model\AnalyticsLog;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Stores to database
 *
 */
class DatabaseHandler extends AbstractProcessingHandler
{
    protected $container;

    /**
     * @param integer $level The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
     *
     * @param type $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        /**
         * Ensure the doctrine channel is ignored (unless its greater than
         * a warning error), otherwise you will create an infinite loop, as
         * doctrine like to log.. a lot..
         */
        if( 'doctrine' == $record['channel'] ) {
            if( (int)$record['level'] >= Logger::WARNING ) {
                error_log($record['message']);
            }

            return;
        }
        /**
         * Only log errors greater than a warning
         * TODO - we should ideally add this into configuration variable
         */
        if( (int)$record['level'] >= Logger::INFO ) {
            try
            {
                $em = $this->container->get('doctrine')->getEntityManager();
                $analyticsLog = new AnalyticsLog();
                $analyticsLog->setLog($record['message'])
                    ->setServerData($record['server_data'])
                    ->setLevel($record['level'])
                    ->setUri($record['uri'])
                    ->setTemplate($record['template'])
                    ->setDuration($record['duration'])
                    ->setMemory($record['memory'])
                    ->setModifiedValue()
                    ->setCreatedValue();

                $em->persist($analyticsLog);
                $em->flush(); 

            } catch( \Exception $e ) {
                print($e->getMessage());
                // Fallback to just writing to php error logs if something really bad happens
                error_log($record['message']);
                error_log($e->getMessage());
            }
        }
    }
}
