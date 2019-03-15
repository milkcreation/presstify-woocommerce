<?php

/**
 * REQUETE DE RECUPERATION DES ELEMENTS DE PAGE
 * @see Woocommerce/includes/class-wc-query.php
 */

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\Contracts\Query as QueryContract;
use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;
use WP_Query;

class Query implements QueryContract
{
    use WoocommerceResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('pre_get_posts', function (WP_Query &$wpQuery) {
            if (!is_admin() && $wpQuery->is_main_query()) :
                foreach ($this->routing()->getRoutes() as $route) :
                    if ($this->routing()->is($route)) :
                        call_user_func_array([$this, 'get_posts'], [&$wpQuery, $route]);
                        if (method_exists($this, 'get_posts_' . $route)) :
                            call_user_func_array([$this, 'get_posts_' . $route], [&$wpQuery]);
                        endif;
                    endif;
                endforeach;
            endif;
        }, 99);
    }

    /**
     * Court-circuitage par défaut de la requête de récupération.
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     * @param string $tag Nom du contexte.
     *
     * @return void
     */
    public function get_posts(WP_Query &$wpQuery, $tag)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_shop()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_shop(WP_Query &$wpQuery)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_product()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_product(WP_Query &$wpQuery)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_product_category()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_product_category(WP_Query &$wpQuery)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_product_tag()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_product_tag(WP_Query &$wpQuery)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_cart()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_cart(WP_Query &$wpQuery)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_checkout()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_checkout(WP_Query &$wpQuery)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_account_page()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_account_page(WP_Query &$wpQuery)
    {

    }
}