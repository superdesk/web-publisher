<?php

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
