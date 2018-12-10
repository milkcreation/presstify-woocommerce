<?php

use tiFy\Plugins\Woocommerce\Cart\Cart;
use tiFy\Plugins\Woocommerce\Functions\Functions;
use tiFy\Plugins\Woocommerce\Shortcodes\Shortcodes;
//use tiFy\Plugins\Woocommerce\MultiShop\Multishop;

if (!function_exists('tify_wc_cart')) :
    /**
     * Classe de rappel de traitement du panier.
     *
     * @return Cart
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
     */
    function tify_wc_cart_contents_count()
    {
        /** @var Functions $functions */
        $functions = app()->get('woocommerce.functions');

        return $functions->cartContentsCount();
    }
endif;

if (!function_exists('tify_wc_do_shortcode')) :
    /**
     * Execution d'un shortcode WooCommerce en dehors de la boucle.
     *
     * @param string $shortcode Nom du shortcode.
     * @param array $attrs Attributs du shortcode.
     *
     * @see class-wc-shortcodes.php
     *
     * @return mixed
     */
    function tify_wc_do_shortcode($shortcode, $attrs = [])
    {
        /** @var Shortcodes $shortcodes */
        $shortcodes = app()->get('woocommerce.shortcodes');

        return $shortcodes->do($shortcode, $attrs = []);
    }
endif;

/**
 * MULTIBOUTIQUE
 */
/**
 * Récupération de l'identifiant de la boutique courante.
 *
 * @return string

function tify_wc_multi_current_shop_id()
{
    return Multishop::getCurrentShopId();
}*/

/**
 * Récupération de la page d'accroche d'une boutique déclarée.
 *
 * @param string $shop_id identifiant de déclaration de la boutique.
 *
 * @return int $post_id

function tify_wc_multi_get_hook_id($shop_id)
{
    return Multishop::getShopHookId($shop_id);
}*/

/**
 * Récupération de la page d'accroche de la boutique courante.
 *
 * @return int $post_id

function tify_wc_multi_current_hook_id()
{
    return Multishop::getCurrentShopHookId();
}*/

/**
 * Vérifie si la page courante est l'accroche d'une boutique.
 *
 * @return bool

function tify_wc_multi_is_hook($shop_id = null)
{
    return Multishop::isShopHook($shop_id);
}*/

/**
 * Récupération l'identifiant de la catégorie d'accroche d'une boutique déclarée.
 *
 * @param string $shop_id identifiant de déclaration de la boutique.
 *
 * @return int $term_id

function tify_wc_multi_get_term_id($shop_id)
{
    return Multishop::getShopTermId($shop_id);
}*/

/**
 * Récupération de la catégorie d'accroche d'une boutique déclarée.
 *
 * @param string $shop_id identifiant de déclaration de la boutique.
 *
 * @return obj $term

function tify_wc_multi_get_term($shop_id)
{
    return Multishop::getShopTerm($shop_id);
}*/

/**
 * Récupération l'identifiant de la catégorie d'accroche d'une boutique courante.
 *
 * @return int $term_id

function tify_wc_multi_current_term_id()
{
    return Multishop::getCurrentShopTermId();
}*/

/**
 * Récupération de la catégorie d'accroche de la boutique courante.
 *
 * @return obj $term

function tify_wc_multi_current_term()
{
    return Multishop::getCurrentShopTerm();
}*/

/**
 * Vérifie si la page courante fait partie de l'ecosystème d'une boutique déclarée.
 *
 * @param string $shop_id identifiant de déclaration de la boutique.
 *
 * @return bool

function tify_wc_multi_in_shop($shop_id)
{
    return Multishop::inShop($shop_id);
}*/