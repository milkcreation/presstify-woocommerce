<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface ProductCat extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * @inheritDoc
     */
    public function boot(): void;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): ProductCat;
}