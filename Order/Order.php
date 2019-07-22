<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Order;

use tiFy\Plugins\Woocommerce\{Contracts\Order as OrderContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

class Order extends ParamsBag implements OrderContract
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
    public function parse(): OrderContract
    {
        parent::parse();

        return $this;
    }
}