<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Cart;

use tiFy\Plugins\Woocommerce\Contracts\Cart as CartContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use tiFy\Support\ParamsBag;

class Cart extends ParamsBag implements CartContract
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
    public function parse(): CartContract
    {
        parent::parse();

        return $this;
    }
}