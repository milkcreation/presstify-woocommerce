<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Cart;

use tiFy\Plugins\Woocommerce\Contracts\Cart as CartContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;

class Cart implements CartContract
{
    use WoocommerceAwareTrait;
}