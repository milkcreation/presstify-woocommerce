<?php
namespace tiFy\Plugins\WooCommerce;

/**
 * IDENTIFIANTS DE CONTEXTE
 * @see woocommerce/includes/wc-conditional-functions.php
 * @see https://docs.woocommerce.com/document/conditional-tags/
 */
class ConditionalTags extends \tiFy\App\Factory
{
    /**
     * Liste des identifiants de contexte autorisés
     * @see https://docs.woocommerce.com/document/conditional-tags/ 
     */
    protected static $Tags                  = array(
        'woocommerce', 
        'shop',         
        'product_taxonomy',
        'product_category', 
        'product_tag',  
        'product',
        'cart', 
        'checkout',
        'checkout_pay_page',
        'account_page',
        'view_order_page',
        'edit_account_page',
        'add_payment_method_page',
        'lost_password_page'
    );
    
    /**
     * Récupération de la liste des identifiants
     */
    final public static function getAll()
    {
        return self::$Tags;
    }
    
    /**
     * Vérifie si un identifiant correspond à un contexte
     */
    final public static function is( $tag )
    {
        return in_array( $tag, self::getAll() );
    }

    /**
     * Ajout d'un contexte personnalisé
     *
     * @param $tag
     */
    final public static function addCustom($tag)
    {
        if (!in_array($tag, self::getAll())) :
            self::$Tags[] = $tag;
        endif;
    }

    /**
     * Récupération des contexte de la page courante
     * 
     * @return array tag de la page courante | false si la page courante n'est pas un contexte Woocommerce
     */
    final public static function current()
    {        
        $context = array();
        foreach( self::getAll() as $tag ) :
           if( is_callable( 'is_'. $tag ) && @ call_user_func( 'is_'. $tag ) ) :
               array_push( $context, $tag );
           endif;
        endforeach;
        
        if( empty( $context ) )
            return false;
        
        return $context;    
    }
    
    /**
     * Vérifie si la page courante correspond au contexte
     * 
     * @return bool
     */
    final public static function isCurrent( $context )
    {
        if( $current = self::current() ) 
            return in_array( $context, (array) $current );
    }
}