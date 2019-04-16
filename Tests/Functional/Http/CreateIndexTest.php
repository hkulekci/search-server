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

namespace Apisearch\Server\Tests\Functional\Http;

use Apisearch\Server\Tests\Functional\HttpFunctionalTest;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CreateIndexTest.
 */
class CreateIndexTest extends HttpFunctionalTest
{
    /**
     * Test check health with different tokens.
     */
    public function testCreateIndex(): void
    {
        $this->deleteIndex(
            self::$appId,
            self::$index
        );

        $client = $this->createClient();
        $testRoute = $this->get('router')->generate('apisearch_v1_put_index', [
            'token' => self::$godToken,
            'app_id' => self::$appId,
            'index_id' => self::$index,
        ]);

        $client->request(
            'put',
            $testRoute,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(
            JsonResponse::HTTP_CREATED,
            $response->getStatusCode()
        );

        $anotherClient = $this->createClient();
        $anotherClient->request(
            'put',
            $testRoute,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $anotherResponse = $anotherClient->getResponse();
        $this->assertEquals(
            JsonResponse::HTTP_CONFLICT,
            $anotherResponse->getStatusCode()
        );
    }
}
