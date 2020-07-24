<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

interface Functions
{
    /**
     * Encapsulation HTML de la décimal d'un prix
     * @todo Séparateur des milliers non géré
     *
     * @param string $price
     *
     * @return string
     */
    public function priceWrapDecimal($price, $args = []);

    /**
     * Retourne le nombre d'article dans le panier.
     *
     * @return int
     */
    public function cartContentsCount();
}