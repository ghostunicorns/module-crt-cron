<?php
/**
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Model;

use GhostUnicorns\CrtBase\Exception\CrtException;
use GhostUnicorns\CrtCron\Api\CronListInterface;
use GhostUnicorns\CrtCron\Api\CrtCronInterface;

class CronList implements CronListInterface
{
    /**
     * @var string[]
     */
    protected $list;

    /**
     * @param array $list
     * @throws CrtException
     */
    public function __construct(
        array $list = []
    ) {
        foreach ($list as $crtCron) {
            if (!$crtCron instanceof CrtCronInterface) {
                throw new CrtException(__("Invalid type for CrtCron"));
            }
        }
        $this->list = $list;
    }

    /**
     * {@inheritdoc}
     */
    public function getlist(): array
    {
        return $this->list;
    }
}
