<?php

namespace tiFy\Plugins\Woocommerce\Checkout;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Checkout as CheckoutContract;

class Checkout extends ParamsBag implements CheckoutContract
{
    /**
     * Minimum de commande.
     * @var array
     */
    protected $minPurchase = [
        'rate'     => 0,
        'based_on' => 'subtotal',
        'notice'   => 'Désolé, le montant minimum des commandes est fixé à %s'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        parent::__construct($attrs);

        $this->setMinPurchase($this->get('min_purchase', []));
        $this->bindToProcess($this->minPurchase);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinPurchase($minPurchase)
    {
        if ($minPurchase) :
            $this->minPurchase = array_merge($this->minPurchase, $minPurchase);
            if (!in_array($this->minPurchase['based_on'], ['subtotal', 'subtotal_ex_tax', 'total'])) :
                $this->minPurchase['based_on'] = 'subtotal';
            endif;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function bindToProcess($minPurchase)
    {
        if ($minPurchase['rate']) :
            add_action(
                'woocommerce_before_checkout_process',
                function () use ($minPurchase) {
                    $cartBase = WC()->cart->{$minPurchase['based_on']};
                    if ($cartBase < $minPurchase['rate']) :
                        wc_add_notice(
                            sprintf($minPurchase['notice'], $minPurchase['rate'] . get_woocommerce_currency_symbol()),
                            'error'
                        );
                    endif;
                }
            );
        endif;
    }
}