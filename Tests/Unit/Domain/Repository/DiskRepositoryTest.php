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

namespace Apisearch\Server\Tests\Unit\Domain\Repository;

use Apisearch\Server\Domain\Repository\DiskRepository;
use Apisearch\Server\Domain\Repository\FullRepository;

/**
 * Class DiskRepositoryTest.
 */
class DiskRepositoryTest extends FullRepositoryTest
{
    /**
     * {@inheritdoc}
     */
    protected function getFullRepository(): FullRepository
    {
        $path = '/tmp/apisearch.repository';
        @unlink($path);

        return new DiskRepository($path);
    }
}
