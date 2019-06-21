<?php
namespace tiFy\Plugins\Woocommerce\PaymentGateway;

class PaymentGateway extends \tiFy\App\Factory
{    
    /**
     * Plateformes
     */
    protected static $Gateways          = array();
    
    /**
     * CONSTRUCTEUR
     */
    public function __construct( $gateways = array() )
    {
        foreach( $gateways as $gateway => $attrs ) :
            self::$Gateways[$gateway] = $attrs;
        endforeach;        
        
        add_filter( 'woocommerce_payment_gateways', array( $this, 'woocommerce_payment_gateways' ) );
    }
    
    /**
     * Déclaration de la plateforme de paiement
     */
    public function woocommerce_payment_gateways( $gateways )
    {        
        $_gateways = array();
        foreach( self::$Gateways as $gateway => $attrs ) :
            $key = array_search( $gateway, $gateways );
            
            // Suppression des plateformes déclarées
            if( $attrs === false ) :           
                unset( $gateways[$key] );
            
            // Enregistrement des plateforme nons déclarées
            else :
                if( class_exists( "\\tiFy\\Plugins\\Woocommerce\\PaymentGateway\\{$gateway}" ) ) :
                    $gateway = self::getOverride( "\\tiFy\\Plugins\\Woocommerce\\PaymentGateway\\{$gateway}" );
                elseif( class_exists( self::getOverrideNamespace() ."\\Plugins\\Woocommerce\\PaymentGateway\\{$gateway}" ) && is_subclass_of( self::getOverrideNamespace() ."\\Plugins\\Woocommerce\\PaymentGateway\\{$gateway}", $gateway ) ) :
                    $gateway = self::getOverrideNamespace() ."\\Plugins\\Woocommerce\\PaymentGateway\\{$gateway}";
                endif;
                
                $_gateways[] = $gateway;
            endif;
        endforeach;      
        
        // Traitement des plateformes non déclarées dans la configuration
        $gateways = array_diff( $gateways, array_keys( self::$Gateways ) );
        foreach( $gateways as $gateway ) :
            array_push( $_gateways, $gateway );
        endforeach;
        
        return $_gateways; 
    }
}