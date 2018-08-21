<?php
/**
 * @Overrideable
 * 
 * @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
 * @see http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
 */
namespace tiFy\Plugins\WooCommerce\Admin\Product\MetaBoxes;

class MetaBoxes extends \tiFy\App\Factory
{
    public function __construct()
    {
        parent::__construct();
        
        add_action( 'current_screen', array( $this, 'current_screen' ) );
        
        // Données de produit
        /// Modifications des onglets 
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'woocommerce_product_data_tabs' ) );
        /// Ajout d'onglets personnalisés
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'woocommerce_product_write_panel_tabs' ) );       
        /// Ajout de panneaux d'édition personnalisés
        add_action( 'woocommerce_product_data_panels', array( $this, 'woocommerce_product_data_panels' ) );
        
        /// Onglet Général
        /// @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/views/html-product-data-general.php
        //// Ajout de champs prix
        add_action( 'woocommerce_product_options_pricing', array( $this, 'woocommerce_product_options_pricing' ) );
        /// Ajout de champs téléchargement
        add_action( 'woocommerce_product_options_downloads', array( $this, 'woocommerce_product_options_downloads' ) );
        /// Ajout de champs taxe
        add_action( 'woocommerce_product_options_tax', array( $this, 'woocommerce_product_options_tax' ) );
        /// Ajout de champs additionnels
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'woocommerce_product_options_general_product_data' ) );
        
        /// Onglet Inventaire
        /// @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/views/html-product-data-inventory.php
        //// Ajout de champs sku/UGS (indentification produit)
        add_action( 'woocommerce_product_options_sku', array( $this, 'woocommerce_product_options_sku' ) );
        //// Ajout de champs gestion de stock (si actif)
        add_action( 'woocommerce_product_options_stock_status', array( $this, 'woocommerce_product_options_stock_status' ) );
        //// Ajout de champs Vente individuelle
        add_action( 'woocommerce_product_options_sold_individually', array( $this, 'woocommerce_product_options_sold_individually' ) );
        /// Ajout de champs additionnels
        add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'woocommerce_product_options_inventory_product_data' ) );
        
        //@todo A COMPLETER
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Affichage de l'écran courant
     */
    final public function current_screen( $current_screen )
    {                        
        // Bypass
        if( ! is_a( $current_screen, 'WP_Screen' )  )
            return;
        if( $current_screen->id !== 'product' )
            return;   

        // Déclaration des metadonnées à enregistrer
        // @todo Gestion des meta multi et fonctions de sanitize_callback
        foreach( $this->register_meta() as $k => $meta ) :
            \tify_meta_post_register( $current_screen->id, $meta, true );
        endforeach;
    }
    
    /**
     * CONTROLEURS
     */
    /**
     * Déclaration des métadonnée à enregistrée
     */
    public function register_meta()
    {
        return array();
    }
    
    /**
     * DONNEES PRODUIT
     */
    /**
     * Modification des onglets
     * @param array $tabs
     * @return array
     */
    public function woocommerce_product_data_tabs( $tabs ) 
    {
        return $tabs;
    }
    
    /**
     * Ajout d'onglets personnalisés
     */
    public function woocommerce_product_write_panel_tabs() {}
    
    /**
     * Ajout de panneaux d'édition personnalisés
     */
    public function woocommerce_product_data_panels() {}
    
    /**
     * ONGLET GENERAL
     */
    /**
     * Ajout de champs - PRIX
     */
    public function woocommerce_product_options_pricing() {}
    
    /**
     * Ajout de champs - TÉLÉCHARGEMENT
     */
    public function woocommerce_product_options_downloads() {}
    
    /**
     * Ajout de champs - TAXE
     */
    public function woocommerce_product_options_tax() {}
    
    /**
     * Ajout de champs - ADDITIONNELS
     */
    public function woocommerce_product_options_general_product_data() {}
    
    /**
     * ONGLET INVENTAIRE
     */
    /**
     * Ajout de champs - IDENTIFICATION PRODUIT
     */
    public function woocommerce_product_options_sku() {}
    
    /**
     * Ajout de champs - GESTION DE STOCK
     */
    public function woocommerce_product_options_stock_status() {}
    
    /**
     * Ajout de champs - VENTE INDIVIDUELLE
     */
    public function woocommerce_product_options_sold_individually() {}
    
    /**
     * Ajout de champs - ADDITIONNELS
     */
    public function woocommerce_product_options_inventory_product_data() {}
}