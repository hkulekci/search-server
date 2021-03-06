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

namespace Apisearch\Server\Domain\Middleware;

use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Server\Domain\Event\ExceptionWasCached;
use Apisearch\Server\Exception\StorableException;
use Drift\EventBus\Bus\EventBus;
use Throwable;

/**
 * Class ExceptionsMiddleware.
 */
final class ExceptionsMiddleware
{
    /**
     * @var EventBus
     *
     * Event bus
     */
    protected $eventBus;

    /**
     * ExceptionsMiddleware constructor.
     *
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function execute($command, callable $next)
    {
        return $next($command)
            ->otherwise(function (Throwable $throwable) use ($command) {
                $event = (new ExceptionWasCached(new StorableException(
                    $throwable->getMessage(),
                    (int) $throwable->getCode(),
                    $throwable->getTraceAsString(),
                    $throwable->getFile(),
                    (int) $throwable->getLine()
                )));

                if ($command instanceof WithRepositoryReference) {
                    $event->withRepositoryReference($command->getRepositoryReference());
                }

                return $this
                    ->eventBus
                    ->dispatch($event)
                    ->then(function () use ($throwable) {
                        throw $throwable;
                    });
            });
    }
}
