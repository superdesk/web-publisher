<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Bundle\UserBundle\Util\TokenGenerator;

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
