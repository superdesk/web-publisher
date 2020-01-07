<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

interface AnalyticsReportInterface extends FileInterface, TenantAwareInterface
{
    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ERRORED = 'errored';

    public function setUser(UserInterface $user): void;

    public function getUser(): UserInterface;

    public function getStatus(): string;

    public function setStatus(string $status): void;
}
