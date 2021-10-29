<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtCron\Model;

use GhostUnicorns\CrtBase\Exception\CrtException;
use GhostUnicorns\CrtCron\Api\CronConfigInterface;
use GhostUnicorns\CrtCron\Api\CrtCronInterface;

class CrtCron implements CrtCronInterface
{
    /**
     * @var CronConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @var string
     */
    private $cronExpression;

    /**
     * @param CronConfigInterface $config
     * @param string $instanceName
     * @param string $code
     * @param string $name
     * @throws CrtException
     */
    public function __construct(
        CronConfigInterface $config,
        string $instanceName,
        string $code,
        string $name
    ) {
        $this->config = $config;
        $this->instanceName = $instanceName;
        $this->cronExpression = $this->getCronExpression();
        $this->code = $code;
        $this->name = $name;
        if ($this->isEnabled() && !$this->isValid()) {
            throw new CrtException(__('Invalid Crt Cron'));
        }
    }

    /**
     * @return string
     */
    public function getCronExpression(): string
    {
        return $this->config->getCronExpression();
    }

    private function isValid(): bool
    {
        if (!preg_match(
            "/^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])|\*\/([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]))".
            " (\*|([0-9]|1[0-9]|2[0-3])|\*\/([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])|\*\/".
            "([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])|\*\/([1-9]|1[0-2])) (\*|([0-6])|\*\/([0-6]))$/",
            $this->cronExpression
        )) {
            return false;
        }

        if ($this->code === '') {
            return false;
        }
        if ($this->name === '') {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isCronEnabled();
    }

    /**
     * @return array
     */
    public function getCronData(): array
    {
        return [
            'name' => $this->name,
            'instance' => $this->instanceName,
            'method' => 'execute',
            'schedule' => $this->cronExpression
        ];
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
