<?php declare(strict_types=1);

use tiFy\Plugins\Woocommerce\Contracts\Woocommerce;
use tiFy\Plugins\Woocommerce\Cart\Cart;
use tiFy\Plugins\Woocommerce\Functions\Functions;
use tiFy\Plugins\Woocommerce\Routing\Routing;
use tiFy\Plugins\Woocommerce\Shortcodes\Shortcodes;

if (!function_exists('woocommerce')) {
    /**
     * Instance du plugin.
     *
     * @return Woocommerce
     */
    function woocommerce(): Woocommerce
    {
        return app()->get('woocommerce');
    }
}

// @todo Tester les anciens helpers
if (!function_exists('tify_wc_cart')) :
    /**
     * Classe de rappel de traitement du panier.
     *
     * @return Cart
     *
     * @deprecated
     */
    function tify_wc_cart()
    {
        return app()->get('woocommerce.cart');
    }
endif;

if (!function_exists('tify_wc_price_wrap_decimal')) :
    /**
     * Encapsulation HTML de la décimal d'un prix.
     *
     * @param string $price
     * @param array $args
     *
     *
     * @return string
     *
     * @deprecated
     */
    function tify_wc_price_wrap_decimal($price, $args = [])
    {
        /** @var Functions $functions */
        $functions = app()->get('woocommerce.functions');

        return $functions->priceWrapDecimal($price, $args);
    }
endif;

if (!function_exists('tify_wc_cart_contents_count')) :
    /**
     * Retourne le nombre d'article dans le panier.
     *
     * @return int
     *
     * @deprecated
     */
    function tify_wc_cart_contents_count()
    {
        /** @var Functions $functions */
        $functions = app()->get('woocommerce.functions');

        return $functions->cartContentsCount();
    }
endif;

if (!function_exists('tify_wc_routing')) :
    /**
     * Classe de rappel de traitement de gestion des routes Woocommerce.
     *
     * @return Routing
     *
     * @deprecated
     */
    function tify_wc_routing()
    {
        return app()->get('woocommerce.routing');
    }
endif;

if (!function_exists('tify_wc_do_shortcode')) :
    /**
     * Execution d'un shortcode Woocommerce en dehors de la boucle.
     *
     * @param string $shortcode Nom du shortcode.
     * @param array $attrs Attributs du shortcode.
     *
     * @see class-wc-shortcodes.php
     *
     * @return mixed
     *
     * @deprecated
     */
    function tify_wc_do_shortcode($shortcode, $attrs = [])
    {
        /** @var Shortcodes $shortcodes */
        $shortcodes = app()->get('woocommerce.shortcodes');

        return $shortcodes->doing($shortcode, $attrs = []);
    }
endif;