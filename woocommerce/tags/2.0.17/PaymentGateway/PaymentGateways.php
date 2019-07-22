<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

use tiFy\Plugins\Woocommerce\Contracts\PaymentGateways as PaymentGatewaysContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;

class PaymentGateways implements PaymentGatewaysContract
{
    use WoocommerceAwareTrait;

    /**
     * Liste des plateformes déclarées
     */
    protected $gateways = [];
    
    /**
     * CONSTRUCTEUR.
     *
     * @param array $gateways Liste des plateformes de paiement.
     * @return void
     */
    public function __construct(array $gateways = [])
    {
        $this->gateways = $gateways;
        
        add_filter('woocommerce_payment_gateways', function (array $gateways) {
            $_gateways = [];

            foreach($this->gateways as $name => $active) {
                $key = array_search($name, $gateways);

                if ($active === false) {
                    unset($gateways[$key]);
                } elseif($this->manager()->getContainer()->has("woocommerce.payment-gateway.{$name}")) {
                    $_gateways[] = $this->manager()->resolve("payment-gateway.{$name}");
                }
            }

            // Traitement des plateformes non déclarées dans la configuration
            $gateways = array_diff($gateways, array_keys($this->gateways));
            foreach($gateways as $gateway) {
                array_push($_gateways, $gateway);
            }

            return $_gateways;
        });
    }
}