<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Api;

interface CronInstanceInterface
{
    /**
     * @return mixed
     */
    public function execute();
}
