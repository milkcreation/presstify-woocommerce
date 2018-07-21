<?php
/*
Plugin Name: Woocommerce
Plugin URI: http://presstify.com/plugins/woocommerce
Description: Support de Woocommerce
Version: 1.0.2
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * PLUGIN DE SUPPORT WOOCOMMERCE
 * @see https://docs.woocommerce.com/wc-apidocs/index.html
 */
namespace tiFy\Plugins\WooCommerce;

use tiFy\App\Plugin;

class WooCommerce extends Plugin
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        parent::__construct();

        // Déclenchement des événements
        $this->appAddAction('init', null, 1);

        // Chargement des controleurs
        // Identifiants de contexte
        new ConditionalTags;

        // Tests et modification des emails
        $Forms = self::getOverride('\tiFy\Plugins\WooCommerce\Emails');
        new Emails(self::tFyAppConfig('emails'));

        // Modification des formulaires
        $Forms = self::getOverride( '\tiFy\Plugins\WooCommerce\Forms' );
        new $Forms( self::tFyAppConfig( 'forms' ) );
        
        // Requête de récupération des éléments de la boutique
        self::loadOverride( '\tiFy\Plugins\WooCommerce\Query' );
        
        // Chargement des scripts
        // Commande
        $ScriptLoader = self::getOverride( '\tiFy\Plugins\WooCommerce\ScriptLoader' );
        new $ScriptLoader( self::tFyAppConfig( 'script_loader' ) );
        
        // Gestionnaire de shortcodes
        new Shortcodes( self::tFyAppConfig( 'shortcodes' ) );
        
        // Eléments de templates
        self::loadOverride('\tiFy\Plugins\WooCommerce\Template');
        
        // Surchage des fonctions de template Woocommerce
        include self::tFyAppDirname() .'/TemplateFunctions.php';
        self::getOverrideAppFile('TemplateFunctions.php');

        // Accrochage / Décrochage / Ordonnacement des éléments de template
        $templateHooks = self::getOverride('\tiFy\Plugins\WooCommerce\TemplateHooks');
        new $templateHooks( self::tFyAppConfig( 'template-hooks' ) );
        
        // Panier
        $Cart = self::loadOverride( '\tiFy\Plugins\WooCommerce\Cart' );
        
        // Livraison
        $Shipping = self::getOverride( '\tiFy\Plugins\WooCommerce\Shipping' );
        new $Shipping( self::tFyAppConfig('shipping'));
        
        // Commande
        $Checkout = self::getOverride( '\tiFy\Plugins\WooCommerce\CheckOut' );
        new $Checkout( self::tFyAppConfig('checkout') );
        
        // Paiement
        self::loadOverride( '\tiFy\Plugins\WooCommerce\Order' );
                
        // Plateformes de paiement    
        if( self::tFyAppConfig( 'payment_gateway' ) ) :
            new PaymentGateway\PaymentGateway( self::tFyAppConfig( 'payment_gateway' ) );
        endif;
        
        // Boutique multiple
        if( self::tFyAppConfig( 'multishop' ) ) :
            new MultiShop\MultiShop( self::tFyAppConfig( 'multishop' ) );
        endif;

        // Chargement des templates avec le moteur de gabarit PHP Plates
        if($this->appServiceHas('tiFyApp')) :
            new TemplateLoader($this->appServiceGet('tiFyApp'));
        endif;

        // Fonctions d'aide à la saisie
        include self::tFyAppDirname() .'/Helpers.php';
        
        // Interface d'administration
        self::loadOverride( '\tiFy\Plugins\WooCommerce\Admin\Product\MetaBoxes\MetaBoxes' );
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation globale de Wordpress
     *
     * @return void
     */
    public function init()
    {
        add_theme_support('woocommerce');
    }
}
