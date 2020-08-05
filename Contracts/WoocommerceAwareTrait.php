<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

interface WoocommerceAwareTrait
{
    /**
     * Récupération de l'instance du gestionnaire du plugin.
     *
     * @return Woocommerce
     */
    public function manager();

    /**
     * Définition de l'instance du gestionnaire du plugin.
     *
     * @param Woocommerce $manager
     *
     * @return static
     */
    public function setManager(Woocommerce $manager): WoocommerceAwareTrait;
}
