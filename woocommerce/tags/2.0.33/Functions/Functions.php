<?php

namespace tiFy\Plugins\Woocommerce\Functions;

use tiFy\Plugins\Woocommerce\Contracts\Functions as FunctionsContract;

class Functions implements FunctionsContract
{
    /**
     * @inheritDoc
     */
    public function priceWrapDecimal($price, $args = [])
    {
        if (!$num_decimals = get_option('woocommerce_price_num_decimals', 0))
            return false;

        $defaults = [
            'wrap' => "<sub>%d</sub>"
        ];
        $args = wp_parse_args($args, $defaults);

        $wrap = preg_replace('/\%d/', '\$2', $args['wrap']);
        $decimal_sep = get_option('woocommerce_price_decimal_sep', '.');

        return preg_replace('/([\d]+' . $decimal_sep . ')([\d]{2})/', '$1' . $wrap, $price);
    }

    /**
     * @inheritDoc
     */
    public function cartContentsCount()
    {
        global $woocommerce;

        return $woocommerce->cart->cart_contents_count;
    }
}