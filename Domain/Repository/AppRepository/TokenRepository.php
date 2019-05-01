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

namespace Apisearch\Server\Domain\Repository\AppRepository;

use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;

/**
 * Interface TokenRepository.
 */
interface TokenRepository
{
    /**
     * Add token.
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     */
    public function addToken(
        RepositoryReference $repositoryReference,
        Token $token
    );

    /**
     * Delete token.
     *
     * @param RepositoryReference $repositoryReference
     * @param TokenUUID           $tokenUUID
     */
    public function deleteToken(
        RepositoryReference $repositoryReference,
        TokenUUID $tokenUUID
    );

    /**
     * Get tokens.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return Token[]
     */
    public function getTokens(RepositoryReference $repositoryReference): array;

    /**
     * Delete all tokens.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function deleteTokens(RepositoryReference $repositoryReference);
}
