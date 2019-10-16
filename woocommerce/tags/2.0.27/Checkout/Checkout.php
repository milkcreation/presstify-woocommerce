<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Checkout;

use tiFy\Plugins\Woocommerce\{Contracts\Checkout as CheckoutContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

class Checkout extends ParamsBag implements CheckoutContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('woocommerce_before_checkout_process', function () {
            if ($this->get('min', [])) {
                $based = $this->get('min.based', 'subtotal');
                $cartBase = WC()->cart->{$based};

                if ($cartBase < $this->get('min.rate')) {
                    wc_add_notice(
                        sprintf($this->get('min.notice'), $this->get('min.rate') . get_woocommerce_currency_symbol()),
                        'error'
                    );
                }
            }
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
    public function parse(): CheckoutContract
    {
        parent::parse();

        if ($min = $this->get('min')) {
            $this->set('min', array_merge([
                'rate'     => 0,
                'based'    => 'subtotal',
                'notice'   => __('Désolé, le montant minimum des commandes est fixé à %s', 'tify')
            ], $min));

            if (!in_array($this->get('min.based'), ['subtotal', 'subtotal_ex_tax', 'total'])) {
                $this->set('min.based', 'subtotal');
            }
        }

        return $this;
    }
}