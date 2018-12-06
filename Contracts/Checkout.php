<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Checkout extends ParamsBag
{
    /**
     * Définition d'un montant minimum de commande.
     *
     * @param array $minPurchase Montant minimum de commande.
     *
     * @return void
     */
    public function setMinPurchase($minPurchase);

    /**
     * Branchement du montant minimum de commande à l'environnement WooCommerce.
     *
     * @param array $minPurchase Montant minimum de commande.
     *
     * @return void
     */
    public function bindToProcess($minPurchase);
}