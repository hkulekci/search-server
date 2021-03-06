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

namespace Apisearch\Server\Domain\Command;

use Apisearch\Model\Token;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\AppRequiredCommand;
use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\LoggableCommand;

/**
 * Class PutToken.
 */
class PutToken extends CommandWithRepositoryReferenceAndToken implements LoggableCommand, AsynchronousableCommand, AppRequiredCommand
{
    /**
     * @var Token
     *
     * Token
     */
    private $newToken;

    /**
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     * @param Token               $newToken
     */
    public function __construct(
        RepositoryReference $repositoryReference,
        Token $token,
        Token $newToken
    ) {
        parent::__construct(
            $repositoryReference,
            $token
        );

        $this->newToken = $newToken;
    }

    /**
     * Get new Token.
     *
     * @return Token
     */
    public function getNewToken(): Token
    {
        return $this->newToken;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'repository_reference' => $this
                ->getRepositoryReference()
                ->compose(),
            'token' => $this
                ->getToken()
                ->toArray(),
            'new_token' => $this
                ->newToken
                ->toArray(),
        ];
    }

    /**
     * Create command from array.
     *
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data)
    {
        return new self(
            RepositoryReference::createFromComposed($data['repository_reference']),
            Token::createFromArray($data['token']),
            Token::createFromArray($data['new_token'])
        );
    }
}
