<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Product;

use tiFy\Support\Proxy\Asset;
use tiFy\Plugins\Woocommerce\{Contracts\Product as ProductContract, Contracts\QueryProduct, WoocommerceAwareTrait};
use WC_Product;
use WP_Post;

class Product implements ProductContract
{
    use WoocommerceAwareTrait;

    /**
     * Instance des produits.
     * @var QueryProduct[]
     */
    protected $products = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('wp', function () {
            if (is_product() && ($product = $this->get())) {
                Asset::setDataJs('woocommerce', ['product' => $product->getDatas()]);
            }
        }, 99);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * Récupération d'un produit.
     *
     * @param null|int
     *
     * @return QueryProduct|null
     */
    public function get(?int $product_id = null): ?QueryProduct
    {
        if (is_null($product_id)) {
            global $product, $post;

            if ($product instanceof WC_Product) {
                $product_id = $product->get_id();
            } elseif ($post instanceof WP_Post && ($post->post_type === 'product')) {
                $product_id = $post->ID;
            } else {
                return null;
            }
        }

        if ($product_id) {
            return $this->products[$product_id] =
                $this->products[$product_id] ?? $this->manager->queryProduct($product_id);
        } else {
            return null;
        }
    }
}