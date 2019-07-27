<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\ProductCat;

use tiFy\Plugins\Woocommerce\Contracts\ProductCat as ProductCatContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use tiFy\Support\ParamsBag;

class ProductCat extends ParamsBag implements ProductCatContract
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
    public function parse(): ProductCatContract
    {
        parent::parse();

        return $this;
    }
}