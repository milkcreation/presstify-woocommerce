<?php
/**
 * @Overrideable
 */
namespace tiFy\Plugins\Woocommerce;

/**
 * ELEMENTS DE TEMPLATES
 * @see woocommerce/includes/wc-template-functions.php
 */
class Template extends \tiFy\App\Factory
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        parent::__construct();
        
        // Court-circuitage des attributs du fil d'Ariane
        add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'woocommerce_breadcrumb_defaults' ) );
    }
    
    /**
     * BREADCRUMB
     */
    /**
     * Court-circuitage des attributs du fil d'Ariane
     * 
     * @param array $args
            array(
    			'delimiter'   => '&nbsp;&#47;&nbsp;',
    			'wrap_before' => '<nav class="woocommerce-breadcrumb">',
    			'wrap_after'  => '</nav>',
    			'before'      => '',
    			'after'       => '',
    			'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
            )
     * @return array
     */
    public function woocommerce_breadcrumb_defaults( $args = array() )
    {
        return $args;
    }
}