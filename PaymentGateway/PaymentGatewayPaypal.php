<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

use tiFy\Plugins\Woocommerce\Contracts\PaymentGatewayPaypal as PaymentGatewayPaypalContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use WC_Gateway_Paypal;

/**
 * @see https://docs.woocommerce.com/document/paypal-standard/
 */
class PaymentGatewayPaypal extends WC_Gateway_Paypal implements PaymentGatewayPaypalContract
{
    use WoocommerceAwareTrait;
}