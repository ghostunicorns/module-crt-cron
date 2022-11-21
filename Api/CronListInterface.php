<?php
/**
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Api;

interface CronListInterface
{
    /** @var string */
    const CRON_GROUP_NAME = 'crt';

    /**
     * @return CrtCronInterface[]
     */
    public function getList(): array;
}
