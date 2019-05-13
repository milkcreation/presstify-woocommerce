<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\Contracts\QueryProducts as QueryProductsContract;
use tiFy\Support\Collection;
use WC_Product;
use WP_Post;
use WP_Query;

class QueryProducts extends Collection implements QueryProductsContract
{
    /**
     * Instance de la requête Wordpress de récupération des posts.
     * @var WP_Query
     */
    protected $wp_query;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Query $wp_query Requête Wordpress de récupération de post.
     *
     * @return void
     */
    public function __construct(WP_Query $wp_query)
    {
        $this->wp_query = $wp_query;

        $items = $wp_query->posts;
        array_walk($items, function(WP_Post &$item){
            $item = WC()->product_factory->get_product($item);
        });

        array_walk($items, [$this, 'walk']);
    }

    /**
     * @inheritDoc
     */
    public static function createFromArgs($args = []): QueryProductsContract
    {
        return new static(new WP_Query($args));
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobals(): QueryProductsContract
    {
        global $wp_query;

        return new static($wp_query);
    }

    /**
     * @inheritDoc
     */
    public static function createFromIds(array $ids): QueryProductsContract
    {
        return new static(new WP_Query(['post__in' => $ids, 'post_type' => ['product', 'product_variation'], 'posts_per_page' => -1]));
    }

    /**
     * @inheritDoc
     */
    public function getIds() : array
    {
        return $this->pluck('ID');
    }

    /**
     * {@inheritDoc}
     *
     * @param WC_Product $item Objet Product Wordpress.
     *
     * @return void
     */
    public function walk($item, $key = null)
    {
        $this->items[$key] = app()->get('woocommerce.query.product', [$item]);
    }

    /**
     * @inheritDoc
     */
    public function WpQuery(): WP_Query
    {
        return $this->wp_query;
    }
}