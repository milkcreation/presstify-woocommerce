<?php
/**
 * @Overrideable
 */
namespace tiFy\Plugins\Woocommerce;

class CheckOut extends \tiFy\App\Factory
{
    /**
     * Minimum de commande
     */
    protected static $MinPurchase  = [
        'rate'          => 0,
        'based_on'      => 'subtotal',
        'notice'        => 'Désolé, le montant minimum des commandes est fixé à %s'
    ];
    
    /**
     * CONSTRUCTEUR
     */
    public function __construct($attrs = [])
    {
        parent::__construct();
        
        // Traitement des attributs
        /// Minimum de commande
        if (! empty($attrs['mininum_purchase'])) :
            self::$MinPurchase = wp_parse_args($attrs['mininum_purchase'], self::$MinPurchase);
            if (! in_array(self::$MinPurchase['based_on'], ['subtotal','subtotal_ex_tax','total'])) :
                self::$MinPurchase['based_on'] = 'subtotal';
            endif;
        endif;
        
        // Initialisation du minimum de commande
        if (self::$MinPurchase['rate']) :
            add_action('woocommerce_before_checkout_process', [$this, 'minimum_purchase']);
        endif;
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Définition du montant minimum de commande
     */
    public function minimum_purchase() 
    {
        $min_purchase   = self::$MinPurchase['rate'];
        $based_on       = self::$MinPurchase['based_on'];
        $cart_base      = WC()->cart->{$based_on};
        
    	if ($cart_base < $min_purchase) :
    		wc_add_notice( 
		        sprintf(self::$MinPurchase['notice'], $min_purchase . get_woocommerce_currency_symbol()),
    		    'error'
		    );
    	endif;
    }
}