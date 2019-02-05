<?php

namespace tiFy\Plugins\Woocommerce\Product;

use tiFy\Plugins\Woocommerce\Contracts\Query as QueryContract;
use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;
use WC_Product_Variable;

class Product implements QueryContract
{
    use WoocommerceResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('wp', function () {
            if (is_product()) :
                global $post;

                $product = WC()->product_factory->get_product($post);

                if ($product instanceof WC_Product_Variable) :
                    assets()->setDataJs('wc_product_variations', $product->get_available_variations());
                endif;
            endif;
        }, 99);
    }
}