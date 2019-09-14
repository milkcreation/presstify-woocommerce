<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

use tiFy\Plugins\Woocommerce\Contracts\PaymentGatewayCheque as PaymentGatewayChequeContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use WC_Gateway_Cheque;

class PaymentGatewayCheque extends WC_Gateway_Cheque implements PaymentGatewayChequeContract
{
    use WoocommerceAwareTrait;
}