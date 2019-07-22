<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

interface Order extends WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;
}