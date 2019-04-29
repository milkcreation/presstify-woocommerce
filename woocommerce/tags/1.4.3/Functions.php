<?php
namespace tiFy\Plugins\WooCommerce;

/**
 * FONCTIONS UTILES
 */
class Functions
{
    /**
     * Encapsulation HTML de la décimal d'un prix
     * @todo Séparateur des milliers non géré
     *  
     * @param string $price
     * @return string
     */
    public static function priceWrapDecimal( $price, $args = array() )
    {
        if( ! $num_decimals = get_option( 'woocommerce_price_num_decimals', 0 ) )
            return false;
                   
        $defaults =   array(
            'wrap'  => "<sub>%d</sub>"
        );
        $args = wp_parse_args( $args, $defaults );
        
        $wrap = preg_replace( '/\%d/', '\$2', $args['wrap'] );
        $decimal_sep = get_option( 'woocommerce_price_decimal_sep', '.' );       
        
        return preg_replace( '/([\d]+'. $decimal_sep .')([\d]{2})/', '$1'. $wrap, $price );
    }
    
    /**
     * Retourne le nombre d'article dans le panier.
     *
     * @return int
     */
    public static function cartContentsCount()
    {
        global $woocommerce;
        
        return $woocommerce->cart->cart_contents_count;
    }
}