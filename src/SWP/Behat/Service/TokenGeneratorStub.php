<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use FOS\UserBundle\Util\TokenGenerator;

class TokenGeneratorStub extends TokenGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generateToken()
    {
        return 'abcdefghijklmn';
    }
}
