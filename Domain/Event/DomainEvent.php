<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Server\Domain\Event;

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Now;
use Carbon\Carbon;

/**
 * Abstract class DomainEvent.
 */
abstract class DomainEvent
{
    /**
     * @var int
     *
     * Occurred on
     */
    private $occurredOn;

    /**
     * @var Carbon;
     *
     * Now
     */
    private $now;

    /**
     * @var RepositoryReference
     */
    private $repositoryReference;

    /**
     * DomainEvent.
     */
    public function __construct()
    {
        $this->setNow();
    }

    /**
     * Mark occurred on as now.
     */
    protected function setNow()
    {
        $this->now = Carbon::now('UTC');
        $this->occurredOn = Now::epochTimeWithMicroseconds($this->now);
    }

    /**
     * Return when event occurred.
     *
     * @return int
     */
    public function occurredOn(): int
    {
        return $this->occurredOn;
    }

    /**
     * Return specific occurred_on ranges.
     *
     * @return int[]
     */
    public function occurredOnRanges(): array
    {
        return [
            'occurred_on_day' => $this->now->startOfDay()->timestamp,
            'occurred_on_week' => $this->now->startOfWeek()->timestamp,
            'occurred_on_month' => $this->now->startOfMonth()->timestamp,
            'occurred_on_year' => $this->now->startOfYear()->timestamp,
        ];
    }

    /**
     * Set repository reference.
     *
     * @param RepositoryReference
     */
    public function withRepositoryReference(RepositoryReference $repositoryReference)
    {
        $this->repositoryReference = $repositoryReference;

        return $this;
    }

    /**
     * Get repository reference.
     *
     * @return RepositoryReference
     */
    public function getRepositoryReference(): RepositoryReference
    {
        return $this->repositoryReference;
    }

    /**
     * to array payload.
     *
     * @return array
     */
    public function toArrayPayload(): array
    {
        return [];
    }

    /**
     * To logger.
     *
     * @return array
     */
    public function toLogger(): array
    {
        return [
            'type' => str_replace('Apisearch\Server\Domain\Event\\', '', get_class($this)),
            'occurred_on' => $this->occurredOn(),
        ] + $this->toArrayPayload();
    }
}
