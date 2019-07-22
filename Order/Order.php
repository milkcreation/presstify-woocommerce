<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Order;

use tiFy\Plugins\Woocommerce\Contracts\Order as OrderContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;

class Order implements OrderContract
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