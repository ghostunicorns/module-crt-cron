<?php
/**
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Api;

interface CronConfigInterface
{
    /**
     * @return bool
     */
    public function isCronEnabled(): bool;

    /**
     * @return string
     */
    public function getCronExpression(): string;
}
