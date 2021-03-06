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

namespace Apisearch\Server\Controller\Listener;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\InvalidTokenException;
use Apisearch\Http\Http;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Server\Controller\RequestAccessor;
use Apisearch\Server\Domain\Token\TokenManager;
use function React\Promise\resolve;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class TokenCheckOverHTTP.
 */
class TokenCheckOverHTTP implements EventSubscriberInterface
{
    /**
     * @var TokenManager
     *
     * Token manager
     */
    private $tokenManager;

    /**
     * @var LoopInterface
     *
     * Loop
     */
    private $loop;

    /**
     * TokenValidationOverHTTP constructor.
     *
     * @param TokenManager  $tokenManager
     * @param LoopInterface $loop
     */
    public function __construct(
        TokenManager $tokenManager,
        LoopInterface $loop
    ) {
        $this->tokenManager = $tokenManager;
        $this->loop = $loop;
    }

    /**
     * Validate token given a Request.
     *
     * @param RequestEvent $event
     *
     * @return PromiseInterface
     */
    public function checkTokenOnKernelRequest(RequestEvent $event): PromiseInterface
    {
        $request = $event->getRequest();
        if ($request->isMethod(Request::METHOD_OPTIONS)) {
            return resolve();
        }

        return resolve()
            ->then(function () use ($request) {
                $query = $request->query;
                $headers = $request->headers;
                $token = $headers->get(
                    Http::TOKEN_ID_HEADER,
                    $query->get(
                        Http::TOKEN_FIELD,
                        ''
                    )
                );

                if (is_null($token)) {
                    throw InvalidTokenException::createInvalidTokenPermissions('');
                }

                $tokenString = $token instanceof Token
                    ? $token->getTokenUUID()->composeUUID()
                    : $token;

                $referer = $request->headers->get('Referer', '');
                $indices = $this->getIndices($request);
                $route = str_replace('apisearch_', '', $request->get('_route'));

                return $this
                    ->tokenManager
                    ->checkToken(
                        AppUUID::createById($request->get('app_id', '')),
                        $indices,
                        TokenUUID::createById($tokenString),
                        $referer,
                        $route
                    );
            })
            ->then(function (Token $token) use ($request) {
                if (!$request->attributes->has('app_id')) {
                    $request
                        ->attributes
                        ->set('app_id', $token
                            ->getAppUUID()
                            ->composeUUID()
                        );
                }

                if (!$request->attributes->has('index_id')) {
                    $indicesAsString = array_map(function (IndexUUID $indexUUID) {
                        return $indexUUID->composeUUID();
                    }, $token->getIndices());

                    $request
                        ->attributes
                        ->set('index_id', implode(',', $indicesAsString));
                }

                return $token;
            })
            ->then(function (Token $token) use ($request) {
                $request
                    ->query
                    ->set(Http::TOKEN_FIELD, $token);
            });
    }

    /**
     * Get index taking in account multiquery.
     *
     * @param Request $request
     *
     * @return IndexUUID
     */
    private function getIndices(Request $request): IndexUUID
    {
        $query = null;
        $indices = [$request->get('index_id', '')];

        try {
            $query = RequestAccessor::extractQuery($request);
        } catch (InvalidFormatException $formatException) {
            return IndexUUID::createById($indices[0]);
        }

        foreach ($query->getSubqueries() as $subquery) {
            if ($subquery->getIndexUUID() instanceof IndexUUID) {
                $indices[] = $subquery->getIndexUUID()->getId();
            }
        }

        $indices = array_unique($indices);

        return IndexUUID::createById(implode(',', $indices));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => [
                ['checkTokenOnKernelRequest', 8],
            ],
        ];
    }
}
