<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\ProductCat;

use tiFy\Plugins\Woocommerce\Contracts\ProductCat as ProductCatContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;

class ProductCat implements ProductCatContract
{
    use WoocommerceAwareTrait;

}