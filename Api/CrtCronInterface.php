<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Api;

interface CrtCronInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return array
     */
    public function getCronData(): array;

    /**
     * @return string
     */
    public function getCronExpression(): string;

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}
