<?php

declare(strict_types=1);

namespace Gitlab;

use Gitlab\Api\Api;

interface ResultPager
{
    /**
     * Fetch a single result (page) from an api call
     *
     * @param Api     $api        the Api instance
     * @param string  $method     the method name to call on the Api instance
     * @param mixed[] $parameters the method parameters in an array
     *
     * @return mixed[] returns the result of the Api::$method() call
     */
    public function fetch(Api $api, string $method, array $parameters = []): array;

    /**
     * Fetch all results (pages) from an api call
     * Use with care - there is no maximum
     *
     * @param Api     $api        the Api instance
     * @param string  $method     the method name to call on the Api instance
     * @param mixed[] $parameters the method parameters in an array
     *
     * @return mixed[] returns a merge of the results of the Api::$method() call
     */
    public function fetchAll(Api $api, string $method, array $parameters = []): array;

    /**
     * Check to determine the availability of a next page
     */
    public function hasNext(): bool;

    /**
     * Check to determine the availability of a previous page
     */
    public function hasPrevious(): bool;

    /**
     * Fetch the next page
     *
     * @return mixed[]
     */
    public function fetchNext(): array;

    /**
     * Fetch the previous page
     *
     * @return mixed[]
     */
    public function fetchPrevious(): array;

    /**
     * Fetch the first page
     *
     * @return mixed[]
     */
    public function fetchFirst(): array;

    /**
     * Fetch the last page
     *
     * @return mixed[]
     */
    public function fetchLast(): array;
}
