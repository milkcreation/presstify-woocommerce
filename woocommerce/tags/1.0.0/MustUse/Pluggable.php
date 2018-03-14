<?php 
use \tiFy\Plugins\WooCommerce\MultiShop\MultiShop;

if (!function_exists('is_shop')) :
    function is_shop()
    {
        $return = false;

        if (!MultiShop::has()) :

            $return = (is_post_type_archive('product') || is_page(wc_get_page_id('shop')));
        elseif ($hook_ids = MultiShop::getShopHookIds()) :
            if ($term_ids = MultiShop::getShopTermIds()) {
                $return = is_tax('product_cat', array_values($term_ids));
            }
            if (!$return) {
                $return = (is_post_type_archive('product') || is_page(array_values($hook_ids)));
            }
        endif;

        return $return; 
    }
endif;