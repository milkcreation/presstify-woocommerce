<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Shipping;

use tiFy\Plugins\Woocommerce\{Contracts\Shipping as ShippingContract, WoocommerceAwareTrait};

class Shipping implements ShippingContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}
}