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

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bridge\Monolog\Processor\WebProcessor;

class RequestProcessor extends WebProcessor
{
    private $_session;

    public function __construct(Session $session)
    {
        $this->_session = $session;
    }

    public function processRecord(array $record)
    {
        $record['extra']['server_data'] = "";

        if( is_array($this->serverData) ) {
            foreach ($this->serverData as $key => $value) {

                if( is_array($value) ) {
                    $value = print_r($value, true);
                }

                $record['extra']['server_data'] .= $key . ": " . $value . "\n";
            }
        }

        foreach ($_SERVER as $key => $value) {

            if( is_array($value) ) {
                $value = print_r($value, true);
            }

            $record['extra']['server_data'] .= $key . ": " . $value . "\n";
        }

        return $record;
    }
}
