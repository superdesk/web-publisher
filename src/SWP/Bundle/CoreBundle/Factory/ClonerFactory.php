<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Factory;

use DeepCopy\DeepCopy;

final class ClonerFactory implements ClonerFactoryInterface
{
    /**
     * @return DeepCopy
     */
    public function create()
    {
        return new DeepCopy();
    }
}
