<?php
/**
 * @Overrideable
 */

/**
 * REQUETE DE RECUPERATION DES ELEMENTS DE PAGE
 * @see woocommerce/includes/class-wc-query.php
 */

namespace tiFy\Plugins\Woocommerce;

use \tiFy\Plugins\Woocommerce\ConditionalTags as Tags;

class Query extends \tiFy\App\Factory
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        parent::__construct();

        // Court-circuitage des requêtes de récupération de produit
        add_action('pre_get_posts', array($this, 'pre_get_posts'), 99);
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Court-circuitage de la requête principale de récupération de produit
     */
    final public function pre_get_posts(&$query)
    {
        if (is_admin()) :
            return;
        endif;

        if ($query->is_main_query()) :
            foreach (Tags::getAll() as $tag) :
                if (!Tags::isCurrent($tag)) {
                    continue;
                }

                call_user_func_array(array($this, 'get_posts'), array(&$query, $tag));
                call_user_func_array(array($this, 'get_posts_' . $tag), array(&$query));
            endforeach;
        endif;
    }

    /**
     * SURCHARGE
     */
    /**
     * Court-circuitage par défaut de la requête de récupération
     */
    public function get_posts(&$query, $tag)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_shop()
     */
    public function get_posts_shop(&$query)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_product()
     */
    public function get_posts_product(&$query)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_product_category()
     */
    public function get_posts_product_category(&$query)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_product_tag()
     */
    public function get_posts_product_tag(&$query)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_cart()
     */
    public function get_posts_cart(&$query)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_checkout()
     */
    public function get_posts_checkout(&$query)
    {

    }

    /**
     * Court-circuitage de la requête de récupération de contexte is_account_page()
     */
    public function get_posts_account_page(&$query)
    {

    }
}