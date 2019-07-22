<?php

/*
use \tiFy\Plugins\Woocommerce\Store\Stores;

if (!function_exists('is_shop')) {
    function is_shop()
    {
        $return = false;

        if (!Stores::has()) :
            $return = (is_post_type_archive('product') || is_page(wc_get_page_id('shop')));
        elseif ($hook_ids = Stores::getShopHookIds()) :
            if ($term_ids = Stores::getShopTermIds()) :
                $return = is_tax('product_cat', array_values($term_ids));
            endif;
            if (!$return) :
                $return = (is_post_type_archive('product') || is_page(array_values($hook_ids)));
            endif;
        endif;

        return $return; 
    }
}