<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

use tiFy\Plugins\Woocommerce\Contracts\PaymentGatewayBacs as PaymentGatewayBacsContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use WC_Gateway_BACS;

class PaymentGatewayBacs extends WC_Gateway_BACS implements PaymentGatewayBacsContract
{
    use WoocommerceAwareTrait;
}