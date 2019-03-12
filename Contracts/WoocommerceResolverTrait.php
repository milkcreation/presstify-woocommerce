<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use WC_Product;
use WP_Query;

interface WoocommerceResolverTrait
{
    /**
     * Instance de traitement des formulaires.
     *
     * @return Form
     */
    public function form();

    /**
     * Instance d'un produit.
     *
     * @param null|WC_Product $wc_product
     *
     * @return QueryProduct
     */
    public function query_product($wc_product = null);

    /**
     * Instance de collection de produit.
     *
     * @param null|WP_Query $wp_query
     *
     * @return QueryProducts|QueryProduct[]
     */
    public function query_products($wp_query = null);

    /**
     * Intance du gestionnaire de routage.
     *
     * @return Routing
     */
    public function routing();

    /**
     * Instance du controleur de gabarit d'affichage.
     *
     * @param null|string Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en arguments au gabarit.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = []);
}