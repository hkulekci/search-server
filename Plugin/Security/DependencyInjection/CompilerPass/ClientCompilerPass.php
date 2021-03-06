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

namespace Apisearch\Plugin\Security\DependencyInjection\CompilerPass;

use Drift\Redis\DependencyInjection\CompilerPass\RedisCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ClientCompilerPass.
 */
class ClientCompilerPass extends RedisCompilerPass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $redisSecurityConfiguration = $container->getParameter('apisearch_plugin.security.redis_configuration');

        $this->createClient(
            $container,
            'security',
            $redisSecurityConfiguration
        );
    }
}
