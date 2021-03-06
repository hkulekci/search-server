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

namespace Apisearch\Server\Domain\Query;

use Apisearch\Server\Domain\Model\CrontabLine;

/**
 * Class GetCrontab.
 */
class GetCrontab
{
    /**
     * @var CrontabLine[]
     *
     * Crontab lines
     */
    private $lines = [];

    /**
     * Add line.
     *
     * @param CrontabLine $line
     */
    public function addLine(CrontabLine $line)
    {
        $this->lines[] = $line;
    }

    /**
     * Get lines.
     *
     * @return CrontabLine[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }
}
