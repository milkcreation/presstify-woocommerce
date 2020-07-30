<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Product;

use tiFy\Support\Proxy\Asset;
use tiFy\Plugins\Woocommerce\{Contracts\Product as ProductContract, Contracts\QueryProduct, WoocommerceAwareTrait};

class Product implements ProductContract
{
    use WoocommerceAwareTrait;

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
     * @inheritDoc
     */
    public function get($id = null): ?QueryProduct
    {
        $instance = $this->manager->resolve('query.product');

        return $instance::create($id);
    }

    /**
     * @inheritDoc
     */
    public function fetch($query = null): array
    {
        $instance = $this->manager->resolve('query.product');

        return $instance::fetch($query);
    }
}