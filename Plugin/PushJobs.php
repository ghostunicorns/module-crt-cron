<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Plugin;

use GhostUnicorns\CrtCron\Api\CronListInterface;
use GhostUnicorns\CrtCron\Model\CronList;
use Magento\Cron\Model\ConfigInterface;

class PushJobs
{
    /**
     * @var CronList
     */
    private $cronList;

    /**
     * @param CronList $cronList
     */
    public function __construct(
        CronList $cronList
    ) {
        $this->cronList = $cronList;
    }

    /**
     * @param ConfigInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterGetJobs(ConfigInterface $subject, $result)
    {
        $list = $this->cronList->getlist();

        if (!count($list)) {
            return $result;
        }

        if (!array_key_exists(CronListInterface::CRON_GROUP_NAME, $result)) {
            $result[CronListInterface::CRON_GROUP_NAME] = [];
        }

        foreach ($list as $crtCron) {
            if ($crtCron->isEnabled()) {
                $result[CronListInterface::CRON_GROUP_NAME][$crtCron->getCode()] = $crtCron->getCronData();
            }
        }

        if (!count($result[CronListInterface::CRON_GROUP_NAME])) {
            unset($result[CronListInterface::CRON_GROUP_NAME]);
        }

        return $result;
    }
}
