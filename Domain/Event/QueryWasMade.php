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

use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;

/**
 * Class QueryWasMade.
 */
final class QueryWasMade extends DomainEvent
{
    /**
     * @var string
     *
     * Query text
     */
    private $queryText;

    /**
     * @var int
     *
     * Size
     */
    private $size;

    /**
     * @var ItemUUID[]
     *
     * Items UUID
     */
    private $itemsUUID;

    /**
     * @var User|null
     *
     * User
     */
    private $user;

    /**
     * @var string
     *
     * Query serialized
     */
    private $querySerialized;

    /**
     * @var int
     */
    private $cost;

    /**
     * QueryWasMade constructor.
     *
     * @param string     $queryText
     * @param int        $size
     * @param ItemUUID[] $itemsUUID
     * @param User|null  $user
     * @param string     $querySerialized
     * @param int        $cost
     */
    public function __construct(
        string $queryText,
        int $size,
        array $itemsUUID,
        ? User $user,
        string $querySerialized,
        int $cost = -1
    ) {
        parent::__construct();
        $this->queryText = $queryText;
        $this->size = $size;
        $this->itemsUUID = $itemsUUID;
        $this->user = $user;
        $this->querySerialized = $querySerialized;
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getQueryText(): string
    {
        return $this->queryText;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return ItemUUID[]
     */
    public function getItemsUUID(): array
    {
        return $this->itemsUUID;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getQuerySerialized(): string
    {
        return $this->querySerialized;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * to array payload.
     *
     * @return array
     */
    public function toArrayPayload(): array
    {
        return [
            'q' => $this->queryText,
            'q_empty' => empty($this->queryText),
            'q_length' => strlen($this->queryText),
            'size' => $this->size,
            'item_uuid' => array_values(
                array_map(function (ItemUUID $itemUUID) {
                    return $itemUUID->composeUUID();
                }, $this->itemsUUID)
            ),
            'result_length' => count($this->itemsUUID),
            'user' => ($this->user instanceof User)
                ? $this->user->toArray()
                : null,
            'query_serialized' => $this->querySerialized,
            'cost' => $this->cost,
        ];
    }
}
