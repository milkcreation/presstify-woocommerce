<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

use tiFy\Plugins\Woocommerce\{Contracts\PaymentGateways as PaymentGatewaysContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

class PaymentGateways extends ParamsBag implements PaymentGatewaysContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_filter('woocommerce_payment_gateways', function (array $gateways) {
            $_gateways = [];

            foreach($this->all() as $name => $active) {
                $key = array_search($name, $gateways);

                if ($active === false) {
                    unset($gateways[$key]);
                } elseif($this->manager()->getContainer()->has("woocommerce.payment-gateway.{$name}")) {
                    $_gateways[] = $this->manager()->resolve("payment-gateway.{$name}");
                }
            }

            // Traitement des plateformes non déclarées dans la configuration
            $gateways = array_diff($gateways, $this->keys());
            foreach($gateways as $gateway) {
                array_push($_gateways, $gateway);
            }

            return $_gateways;
        });

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function parse(): PaymentGatewaysContract
    {
        parent::parse();

        return $this;
    }
}