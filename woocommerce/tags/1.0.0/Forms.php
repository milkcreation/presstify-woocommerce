<?php
/**
 * @Overrideable
 */
namespace tiFy\Plugins\WooCommerce;

/**
 * FORMULAIRES
 * @see woocommerce/includes/wc-template-functions.php
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 */
class Forms extends \tiFy\App\Factory
{        
    /**
     * Remplacement de la liste de selection des pays par tiFyDropdown
     */
    protected static $tiFyDropdownCountry      = false;
    
    /**
     * CONSTRUCTEUR
     */
    public function __construct( $attrs = array() )
    {
        parent::__construct();
        
        // Traitement des attributs
        /// Remplacement de la liste de selection des pays par tiFyDropdown
        if( ! empty( $attrs['tify_dropdown_country'] ) )
            self::$tiFyDropdownCountry = true;        
        
        // Court-circuitage des attributs de champ de formulaire
        add_filter( 'woocommerce_form_field_args', array( $this, 'woocommerce_form_field_args' ), 10, 3 );
        add_filter( 'woocommerce_form_field_tify_dropdown_country', array( $this, 'woocommerce_form_field_tify_dropdown_country' ), 10, 4 ); 
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Court-circuitage des attributs de champ de formulaire
     * 
     * @param array $args
     * @param string $key
     * @param string $value
     * @return mixed
     */
    final public function woocommerce_form_field_args( $args, $key, $value )
    {
        // Remplacement de la liste de selection des pays par tiFyDropdown
        if( in_array( $key, array( 'billing_country', 'shipping_country' ) ) && self::$tiFyDropdownCountry ) :
            $args['type'] = 'tify_dropdown_country';
        endif;
        
        if( method_exists( $this, 'form_field_args_' . $key ) ) :
            return call_user_func( array( $this, 'form_field_args_' . $key ), $args, $value );
        else :
            return call_user_func( array( $this, 'form_fields_args' ), $args, $key, $value );
        endif;
    }
    
    /**
     * 
     * @see wp-content/plugins/woocommerce/includes/wc-template-functions.php
     */
    final public function woocommerce_form_field_tify_dropdown_country ( $field, $key, $args, $value )
    {
        $field = "";
       
        if ( $args['required'] ) :
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
		else :
			$required = '';
		endif;
        
        if ( $args['label'] ) :
            $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . $args['label'] . $required . '</label>';
        endif;    	
         
        $sort = $args['priority'] ? $args['priority'] : '';
		$field_container = '<div class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</div>';        
        $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
        
        if ( 1 === sizeof( $countries ) ) :
            $field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';
            $field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" />';
        else :
            $field .= tify_control_dropdown(
                array(
                    'name'              => esc_attr( $key ),
                    // 'id'                => esc_attr( $args['id'] ), // BUG avec le checkoutjs
                    'class'             => 'country_to_state country_select '. esc_attr( implode( ' ', $args['input_class'] ) ),
                    'attrs'             => array_filter( (array) $args['custom_attributes'] ),  
                    'show_option_none'  => esc_html__( 'Select a country&hellip;', 'woocommerce' ),
                    'option_none_value' => '',
                    'choices'           => $countries,
                    'selected'          => $value,
                    'picker'            => [
                        'class' => 'country_to_state country_select_picker '. (isset($args['picker_class']) ? esc_attr( implode( ' ', $args['picker_class'] ) ) : null)
                    ]
                ),
                false
            );
           $field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'woocommerce' ) . '" /></noscript>';
        endif;
        
        if ( $args['description'] ) :
            $field .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
        endif;

        $container_class = esc_attr( implode( ' ', $args['class'] ) );
        $container_id    = esc_attr( $args['id'] ) . '_field';
        $field           = sprintf( $field_container, $container_class, $container_id, $field );
        
        return $field;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Vérification du remplacement de la liste de choix des pays
     */
    final public static function istiFyDropdownCountry()
    {
        return ! empty( self::$tiFyDropdownCountry );
    }
    
    /**
     * Traitement des arguments par défaut de formulaire
     * 
     * @param array $args
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => ''
     * @param string $key
     * @param string $value
     * 
     * @see woocommerce_form_field()
     */
    public function form_fields_args( $args, $key, $value )
    {
       return $args;  
    }
    
    /**
     * Exemple de traitement de champ -> prénom de l'adresse de facturation
     * 
     * @param array $args
     * @param string $value
     */
    public function form_field_args_billing_first_name( $args, $value )
    {
        return $this->form_fields_args( $args, 'billing_first_name', $value );   
    }    
}