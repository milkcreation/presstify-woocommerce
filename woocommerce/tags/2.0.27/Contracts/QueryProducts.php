<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Wordpress\Contracts\QueryPosts;

interface QueryProducts extends QueryPosts
{
    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public static function createFromArgs($args = []): QueryPosts;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public static function createFromGlobals(): QueryPosts;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public static function createFromIds(array $ids, ...$args): QueryPosts;
}