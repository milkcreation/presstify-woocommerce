<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

use tiFy\Plugins\Woocommerce\Contracts\PaymentGatewayCod as PaymentGatewayCodContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use WC_Gateway_COD;

class PaymentGatewayCod extends WC_Gateway_COD implements PaymentGatewayCodContract
{
    use WoocommerceAwareTrait;
}