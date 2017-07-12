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

use Puntmig\Search\Query\Filter;
use Puntmig\Search\Query\Query;
use Puntmig\Search\Result\Result;

/**
 * Class FiltersTest.
 */
trait FiltersTest
{
    /**
     * Filter by simple fields.
     */
    public function testFilterBySimpleFields()
    {
        $repository = static::$repository;

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByIds(['1'])),
            ['?1', '!2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByIds(['1', '2'])),
            ['?1', '?2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('id', 'id', ['1', '2'])),
            ['?1', '?2', '!3', '!4', '!5']
        );
    }

    /**
     * Filter by metadata fields.
     */
    public function testFilterBydataFields()
    {
        $repository = static::$repository;

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('i', 'field_integer', ['10'], Filter::MUST_ALL)),
            ['?1', '!2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('b', 'field_boolean', ['true'], Filter::MUST_ALL)),
            ['?1', '!2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('k', 'field_keyword', ['my_keyword'], Filter::MUST_ALL)),
            ['?1', '!2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('color', 'color', ['yellow'], Filter::AT_LEAST_ONE)),
            ['!1', '!2', '?3', '!4', '?5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('color', 'color', ['yellow', 'red'], Filter::MUST_ALL)),
            ['!1', '!2', '!3', '!4', '?5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('color', 'color', ['yellow', 'nonexistent'], Filter::MUST_ALL)),
            ['!1', '!2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('color', 'color', ['nonexistent'], Filter::AT_LEAST_ONE)),
            ['!1', '!2', '!3', '!4', '!5']
        );
    }

    /**
     * Test type filter.
     */
    public function testTypeFilter()
    {
        $repository = static::$repository;

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByTypes(['product'])),
            ['?1', '?2', '!3', '!4', '!5', '!800']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterBy('type', 'type', ['product'])),
            ['?1', '?2', '!3', '!4', '!5', '!800']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByTypes(['product', 'book'])),
            ['?1', '?2', '?3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByTypes(['book'])),
            ['!1', '!2', '?3', '!4', '!5']
        );

        $this->assertEmpty(
            $repository->query(Query::createMatchAll()->filterByTypes(['_nonexistent']))->getItems()
        );

        $repository->setKey(self::$anotherKey);
        $this->assertEmpty(
            $repository->query(Query::createMatchAll()->filterByTypes(['product']))->getItems()
        );
    }

    /**
     * Test filter by price range.
     */
    public function testPriceRangeFilter()
    {
        $repository = static::$repository;

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['1000..2000'])),
            ['!1', '?2', '!3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['1000..2001'])->filterByTypes(['book'])),
            ['!1', '!2', '?3', '!4', '!5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['900..1900'])),
            ['?1', '?2', '!3', '!4', '!5']
        );

        $this->assertEmpty(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['100..200']))->getItems()
        );

        $this->assertEmpty(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['0..1']))->getItems()
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['0..-1'])),
            ['?1', '?2', '?3', '?4', '?5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['1..-1'])),
            ['?1', '?2', '?3', '?4', '?5']
        );

        $this->assertResults(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['0..0'])->filterByRange('price', 'price', [], ['0..-1'])),
            ['?1', '?2', '?3', '?4', '?5']
        );

        $repository->setKey(self::$anotherKey);
        $this->assertEmpty(
            $repository->query(Query::createMatchAll()->filterByRange('price', 'price', [], ['0..-1']))->getItems()
        );
    }

    /**
     * Test filter by rangde dates.
     */
    public function testDateRangeFilter()
    {
        $this->assertCount(
            4,
            $this->buildCreatedAtFilteredResult('2010-01-01..2021-01-01')->getItems()
        );

        $this->assertResults(
            $this->buildCreatedAtFilteredResult('2010-01-01..2021-01-01'),
            ['?1', '?2', '?3', '?4', '!5']
        );

        $this->assertResults(
            $this->buildCreatedAtFilteredResult('..2021-01-01'),
            ['?1', '?2', '?3', '?4', '!5']
        );

        $this->assertCount(
            2,
            $this->buildCreatedAtFilteredResult('..2020-03-03')->getItems()
        );

        $this->assertCount(
            3,
            $this->buildCreatedAtFilteredResult('..2020-03-03T23:59:59Z')->getItems()
        );

        $this->assertCount(
            2,
            $this->buildCreatedAtFilteredResult('2020-02-02..2020-04-04')->getItems()
        );

        $this->assertCount(
            3,
            $this->buildCreatedAtFilteredResult('2020-02-02..')->getItems()
        );

        $this->assertCount(
            5,
            $this->buildCreatedAtFilteredResult('..')->getItems()
        );
    }

    /**
     * Build created at filtered Result.
     *
     * @pram string $filter
     *
     * @return Result
     */
    private function buildCreatedAtFilteredResult(string $filter) : Result
    {
        return static::$repository->query(Query::createMatchAll()->filterByDateRange('created_at', 'created_at', [], [$filter], Filter::AT_LEAST_ONE, false));
    }

    /**
     * Test filter by rangde dates.
     */
    public function testUniverseDateRangeFilter()
    {
        $this->assertCount(
            4,
            $this->buildCreatedAtUniverseFilteredResult('2010-01-01..2021-01-01')->getItems()
        );

        $this->assertResults(
            $this->buildCreatedAtUniverseFilteredResult('2010-01-01..2021-01-01'),
            ['?1', '?2', '?3', '?4', '!5']
        );

        $this->assertResults(
            $this->buildCreatedAtUniverseFilteredResult('..2021-01-01'),
            ['?1', '?2', '?3', '?4', '!5']
        );

        $this->assertCount(
            2,
            $this->buildCreatedAtUniverseFilteredResult('..2020-03-03')->getItems()
        );

        $this->assertCount(
            3,
            $this->buildCreatedAtUniverseFilteredResult('..2020-03-03T23:59:59Z')->getItems()
        );

        $this->assertCount(
            2,
            $this->buildCreatedAtUniverseFilteredResult('2020-02-02..2020-04-04')->getItems()
        );

        $this->assertCount(
            3,
            $this->buildCreatedAtUniverseFilteredResult('2020-02-02..')->getItems()
        );

        $this->assertCount(
            5,
            $this->buildCreatedAtUniverseFilteredResult('..')->getItems()
        );

        $this->assertCount(
            1,
            static::$repository->query(Query::createMatchAll()
                ->filterUniverseByDateRange('created_at', ['2020-02-02..2020-04-04'], Filter::AT_LEAST_ONE)
                ->filterByDateRange('created_at', 'created_at', [], ['2020-03-03..2020-04-04'], Filter::AT_LEAST_ONE, false)
            )->getItems()
        );
    }

    /**
     * Build created at filtered Result.
     *
     * @pram string $filter
     *
     * @return Result
     */
    private function buildCreatedAtUniverseFilteredResult(string $filter) : Result
    {
        return static::$repository->query(Query::createMatchAll()->filterUniverseByDateRange('created_at', [$filter], Filter::AT_LEAST_ONE));
    }
}
