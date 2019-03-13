<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Component\Common\Generator\GeneratorInterface;

final class RandomStringGeneratorStub implements GeneratorInterface
{
    public function generate(int $length): string
    {
        return '0123456789abc';
    }
}
