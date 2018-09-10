<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Model\FileInterface;

interface FileProviderInterface
{
    public function getFile(string $id, string $extension): ?FileInterface;
}
