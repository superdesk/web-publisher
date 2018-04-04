<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Component\Common\Generator\GeneratorInterface;

final class RandomStringGeneratorStub implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($length)
    {
        return '0123456789';
    }
}
