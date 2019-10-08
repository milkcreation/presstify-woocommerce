<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Shipping;

use tiFy\Plugins\Woocommerce\{Contracts\Shipping as ShippingContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

class Shipping extends ParamsBag implements ShippingContract
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

    /**
     * @inheritDoc
     */
    public function parse(): ShippingContract
    {
        parent::parse();

        return $this;
    }
}