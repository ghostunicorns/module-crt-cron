<?php
/**
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Cron;

use Exception;
use GhostUnicorns\CrtBase\Exception\CrtException;
use GhostUnicorns\CrtBase\Model\Run\RunAsync;
use GhostUnicorns\CrtCron\Api\CronConfigInterface;
use GhostUnicorns\CrtCron\Api\CronInstanceInterface;
use Monolog\Logger;

class Run implements CronInstanceInterface
{
    /**
     * @var string
     */
    private $activityType;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var CronConfigInterface
     */
    private $cronConfig;

    /**
     * @var RunAsync
     */
    private $runAsync;

    /**
     * @var string
     */
    private $extra;

    /**
     * @param Logger $logger
     * @param CronConfigInterface $cronConfig
     * @param RunAsync $runAsync
     * @param string $activityType
     * @param string $extra
     */
    public function __construct(
        Logger $logger,
        CronConfigInterface $cronConfig,
        RunAsync $runAsync,
        string $activityType,
        string $extra = ''
    ) {
        $this->logger = $logger;
        $this->cronConfig = $cronConfig;
        $this->activityType = $activityType;
        $this->runAsync = $runAsync;
        $this->extra = $extra;
    }

    /**
     * @return void
     * @throws CrtException
     */
    public function execute(): void
    {
        if (!$this->cronConfig->isCronEnabled()) {
            $message = __(
                'Cron job CrtCron activityType:%1 can\'t run because it is disabled',
                $this->activityType
            );
            $this->logger->info($message);
            throw new CrtException($message);
        }

        try {
            $this->runAsync->execute($this->activityType, $this->extra);
        } catch (Exception $e) {
            $this->logger->error(__(
                'Cron ~ Error during runAsync ~ activityType:%1 ~ error:%2',
                $this->activityType,
                $e->getMessage()
            ));
        }
    }
}
