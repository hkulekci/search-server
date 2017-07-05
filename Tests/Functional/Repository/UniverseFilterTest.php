<?php

/*
 * This file is part of the Search Server Bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Puntmig\Search\Server\Tests\Functional\Repository;

use Puntmig\Search\Geo\CoordinateAndDistance;
use Puntmig\Search\Model\Coordinate;
use Puntmig\Search\Query\Filter;
use Puntmig\Search\Query\Query;

/**
 * Trait UniverseFilterTest.
 */
trait UniverseFilterTest
{
    /**
     * Test filtering universe by type.
     */
    public function testFilterUniverseByType()
    {
        $repository = static::$repository;
        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseByTypes(['product'])
                ->aggregateBy('category', 'category_data', Filter::AT_LEAST_ONE)
        );

        $this->assertCount(4, $result->getAggregation('category'));
    }

    /**
     * Test filtering universe by ids.
     */
    public function testFilterUniverseById()
    {
        $repository = static::$repository;
        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseByIds(['2', '3'])
                ->aggregateBy('category', 'category_data', Filter::AT_LEAST_ONE)
        );

        $this->assertCount(3, $result->getAggregation('category'));
    }

    /**
     * Test filtering universe by ids.
     */
    public function testFilterUniverse()
    {
        $repository = static::$repository;
        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseBy('color', ['yellow'], Filter::AT_LEAST_ONE)
                ->aggregateBy('stores', 'stores', Filter::AT_LEAST_ONE)
                ->enableSuggestions()
        );

        $this->assertCount(3, $result->getAggregation('stores'));
        $this->assertCount(2, $result->getItems());
    }

    /**
     * Test filtering universe by range.
     */
    public function testFilterUniverserByRange()
    {
        $repository = static::$repository;
        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseByRange('price', ['10..1000'], Filter::AT_LEAST_ONE)
        );
        $this->assertCount(2, $result->getItems());

        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseByRange('price', ['5..15', '1000..2001'], Filter::AT_LEAST_ONE)
        );
        $this->assertCount(3, $result->getItems());

        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseByRange('price', ['5..', '..20000'], Filter::AT_LEAST_ONE)
        );
        $this->assertCount(5, $result->getItems());
    }

    /**
     * Test filter universe by location.
     */
    public function testFilterUniverseByLocation()
    {
        $repository = static::$repository;
        $result = $repository->query(
            Query::createMatchAll()
                ->filterUniverseByLocation(new CoordinateAndDistance(
                    new Coordinate(45.0, 45.0),
                    '1180km'
                ))
        );
        $this->assertCount(2, $result->getItems());
    }
}
