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

namespace Apisearch\Server;

use Apisearch\Plugin\DBAL\DBALPluginBundle;
use Apisearch\Plugin\Elasticsearch\ElasticsearchPluginBundle;
use Apisearch\Plugin\Logstash\LogstashPluginBundle;
use Apisearch\Plugin\QueryMapper\QueryMapperPluginBundle;
use Apisearch\Plugin\Security\SecurityPluginBundle;
use Apisearch\Plugin\StaticTokens\StaticTokensPluginBundle;
use Apisearch\Server\DependencyInjection\Env;
use Apisearch\Server\Domain\Plugin\Plugin;
use Mmoreram\BaseBundle\BaseBundle;
use Mmoreram\SymfonyBundleDependencies\DependentBundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ApisearchPluginsBundle.
 */
class ApisearchPluginsBundle extends BaseBundle implements DependentBundleInterface
{
    /**
     * Return all bundle dependencies.
     *
     * Values can be a simple bundle namespace or its instance
     *
     * @return array
     */
    public static function getBundleDependencies(KernelInterface $kernel): array
    {
        $pluginsAsString = Env::get('APISEARCH_ENABLED_PLUGINS', '');
        $pluginsAsArray = explode(',', $pluginsAsString);
        $pluginsAsArray = array_map('trim', $pluginsAsArray);
        $pluginsAsArray = self::resolveAliases($pluginsAsArray);

        $pluginsAsArray = array_filter($pluginsAsArray, function (string $pluginNamespace) {
            if (
                empty($pluginNamespace) ||
                !class_exists($pluginNamespace)
            ) {
                return false;
            }

            $reflectionClass = new \ReflectionClass($pluginNamespace);

            return $reflectionClass->implementsInterface(Plugin::class);
        });

        return $pluginsAsArray;
    }

    /**
     * Resolve aliases.
     *
     * @param array $bundles
     *
     * @return array
     */
    private static function resolveAliases(array $bundles): array
    {
        $aliases = [
            'elasticsearch' => ElasticsearchPluginBundle::class,
            'logstash' => LogstashPluginBundle::class,
            'dbal' => DBALPluginBundle::class,
            'static_tokens' => StaticTokensPluginBundle::class,
            'security' => SecurityPluginBundle::class,
            'query_mapper' => QueryMapperPluginBundle::class,
        ];

        $combined = array_combine(
            array_values($bundles),
            array_values($bundles)
        );

        return array_values(
            array_replace(
                $combined,
                array_intersect_key(
                    $aliases,
                    $combined
                )
            )
        );
    }
}
