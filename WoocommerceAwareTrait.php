<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use tiFy\Plugins\Woocommerce\Contracts\Woocommerce;
use tiFy\Plugins\Woocommerce\Contracts\WoocommerceAwareTrait as WoocommerceAwareTraitContract;

/**
 * @mixin WoocommerceAwareTraitContract
 */
trait WoocommerceAwareTrait
{
    /**
     * Instance du gestionnaire de plugin.
     * @var Woocommerce
     */
    protected $manager;

    /**
     * Récupération de l'instance du gestionnaire du plugin.
     *
     * @return Woocommerce
     */
    public function manager(): Woocommerce
    {
        return $this->manager;
    }

    /**
     * Définition de l'instance du gestionnaire du plugin.
     *
     * @return static
     */
    public function setManager(Woocommerce $manager): WoocommerceAwareTraitContract
    {
        $this->manager = $manager;

        return $this;
    }
}
