<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Exception;

final class InvalidResponseException extends \InvalidArgumentException
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Invalid response data.');
    }
}
