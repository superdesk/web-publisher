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
namespace SWP\AnalyticsBundle\Processor;

use Symfony\Bridge\Monolog\Processor\WebProcessor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestProcessor extends WebProcessor
{
    protected $_request;

    public function setRequest(ContainerInterface $container)
    {
        $this->_request = $container->get('request');
    }

    public function processRecord(array $record)
    {
        $requestUri = $this->_request->getUri();

        $record['uri'] = $this->_request->getUri();
        $record['server_data'] = "";

        if( is_array($this->serverData) ) {
            foreach ($this->serverData as $key => $value) {
                if( is_array($value) ) {
                    $value = print_r($value, true);
                }
                $record['server_data'] .= $key . ": " . $value . "\n";
            }
        }

        foreach ($this->_request->request->all() as $key => $value) {
            if( is_array($value) ) {
                $value = print_r($value, true);
            }
            $record['server_data'] .= $key . ": " . $value . "\n";
        }

        return $record;
    }
}
