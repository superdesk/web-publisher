<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Factory;

use DeepCopy\DeepCopy;

interface ClonerFactoryInterface
{
    /**
     * @return DeepCopy
     */
    public function create();
}
