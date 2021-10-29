<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Cron;

use Exception;
use GhostUnicorns\CrtActivity\Model\HasRunningActivity;
use GhostUnicorns\CrtBase\Exception\CrtException;
use GhostUnicorns\CrtBase\Model\Action\CollectAction;
use GhostUnicorns\CrtBase\Model\Action\RefineAction;
use GhostUnicorns\CrtBase\Model\Action\TransferAction;
use GhostUnicorns\CrtCron\Api\CronConfigInterface;
use GhostUnicorns\CrtCron\Api\CronInstanceInterface;
use Monolog\Logger;

class ProcessAll implements CronInstanceInterface
{
    /**
     * @var string
     */
    private $activityType;

    /**
     * @var CollectAction
     */
    private $collectAction;

    /**
     * @var RefineAction
     */
    private $refineAction;

    /**
     * @var TransferAction
     */
    private $transferAction;

    /**
     * @var HasRunningActivity
     */
    private $hasRunningActivity;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var CronConfigInterface
     */
    private $cronConfig;

    /**
     * @param CollectAction $collectAction
     * @param RefineAction $refineAction
     * @param TransferAction $transferAction
     * @param HasRunningActivity $hasRunningActivity
     * @param Logger $logger
     * @param CronConfigInterface $cronConfig
     * @param string $activityType
     */
    public function __construct(
        CollectAction $collectAction,
        RefineAction $refineAction,
        TransferAction $transferAction,
        HasRunningActivity $hasRunningActivity,
        Logger $logger,
        CronConfigInterface $cronConfig,
        string $activityType
    ) {
        $this->collectAction = $collectAction;
        $this->refineAction = $refineAction;
        $this->transferAction = $transferAction;
        $this->hasRunningActivity = $hasRunningActivity;
        $this->logger = $logger;
        $this->cronConfig = $cronConfig;
        $this->activityType = $activityType;
    }

    /**
     * @return void
     * @throws CrtException
     */
    public function execute()
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
            if (!$this->hasRunningActivity->hasCollecting($this->activityType)) {
                $this->collectAction->execute($this->activityType);
            } else {
                $message = __(
                    'Cron ~ There are running collectAction ~ activityType:%1 ~ SKIP',
                    $this->activityType
                );
                $this->logger->error($message);
                throw new CrtException($message);
            }
        } catch (Exception $e) {
            $this->logger->error(__(
                'Cron ~ Error during collectAction ~ activityType:%1 ~ error:%2',
                $this->activityType,
                $e->getMessage()
            ));
        }
        try {
            if (!$this->hasRunningActivity->hasRefining($this->activityType)) {
                while ($this->hasRunningActivity->hasCollected($this->activityType) &&
                    !$this->hasRunningActivity->hasRefining($this->activityType)
                ) {
                    $this->refineAction->execute($this->activityType);
                }
            } else {
                $this->logger->error(__(
                    'Cron ~ There are running collectAction ~ activityType:%1 ~ SKIP',
                    $this->activityType
                ));
            }
        } catch (Exception $e) {
            $this->logger->error(__(
                'Cron ~ Error during refineAction ~ activityType:%1 ~ error:%2',
                $this->activityType,
                $e->getMessage()
            ));
        }
        try {
            if (!$this->hasRunningActivity->hasTransfering($this->activityType)) {
                while ($this->hasRunningActivity->hasRefined($this->activityType) &&
                    !$this->hasRunningActivity->hasTransfering($this->activityType)
                ) {
                    $this->transferAction->execute($this->activityType);
                }
            } else {
                $this->logger->error(__(
                    'Cron ~ There are running collectAction ~ activityType:%1 ~ SKIP',
                    $this->activityType
                ));
            }
        } catch (Exception $e) {
            $this->logger->error(__(
                'Cron ~ Error during transferAction ~ activityType:%1 ~ error:%2',
                $this->activityType,
                $e->getMessage()
            ));
        }
    }
}
