<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;

interface WoocommerceResolverTrait
{
    /**
     * Instance de traitement des formulaires.
     *
     * @return Form
     */
    public function form();

    /**
     * Instance de gestion de produit.
     *
     * @return Product
     */
    public function product();

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