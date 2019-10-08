<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\Contracts\{QueryProduct as QueryProductContract, QueryProducts as QueryProductsContract};
use tiFy\Wordpress\Contracts\QueryPosts as QueryPostsContract;
use tiFy\Wordpress\Query\QueryPosts;
use WC_Product;
use WP_Post;
use WP_Query;

class QueryProducts extends QueryPosts implements QueryProductsContract
{
    /**
     * Instance de la requête Wordpress de récupération des posts.
     * @var WP_Query
     */
    protected $wp_query;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Query|null $wp_query Requête Wordpress de récupération de post.
     *
     * @return void
     */
    public function __construct(?WP_Query $wp_query = null)
    {
        parent::__construct();
        if ($this->wp_query = $wp_query) {
            $items = $wp_query->posts;
            array_walk($items, function (WP_Post &$item, $key) {
                $this->walk(WC()->product_factory->get_product($item), $key);
            });
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductsContract
     */
    public static function createFromArgs($args = []): QueryPostsContract
    {
        return new static(new WP_Query($args));
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductsContract
     */
    public static function createFromGlobals(): QueryPostsContract
    {
        global $wp_query;

        return new static($wp_query);
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductsContract
     */
    public static function createFromIds(array $ids, ...$args): QueryPostsContract
    {
        return new static(new WP_Query([
            'post__in'       => $ids,
            'post_type'      => ['product', 'product_variation'],
            'post_status'    => ['publish', 'private'],
            'posts_per_page' => -1,
        ]));
    }

    /**
     * {@inheritDoc}
     *
     * @param WC_Product $item Objet Product Wordpress.
     *
     * @return void
     */
    public function walk($item, $key = null): void
    {
        if (!$item instanceof QueryProductContract) {
            $item = app()->get('woocommerce.query.product', [$item]);
        }

        $this->items[$key] = $item;
    }
}