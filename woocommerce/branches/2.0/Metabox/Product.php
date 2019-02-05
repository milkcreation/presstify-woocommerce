<?php
/**
 * @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
 * @see http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
 */

namespace tiFy\Plugins\Woocommerce\Metabox;

use tiFy\Plugins\Woocommerce\Contracts\Metabox as MetaboxContract;

class Product implements MetaboxContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('current_screen', [$this, 'current_screen']);

        // Données de produit
        /// Modifications des onglets 
        add_filter('woocommerce_product_data_tabs', [$this, 'woocommerce_product_data_tabs']);
        /// Ajout d'onglets personnalisés
        add_action('woocommerce_product_write_panel_tabs', [$this, 'woocommerce_product_write_panel_tabs']);
        /// Ajout de panneaux d'édition personnalisés
        add_action('woocommerce_product_data_panels', [$this, 'woocommerce_product_data_panels']);

        /// Onglet Général
        /// @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/views/html-product-data-general.php
        //// Ajout de champs prix
        add_action('woocommerce_product_options_pricing', [$this, 'woocommerce_product_options_pricing']);
        /// Ajout de champs téléchargement
        add_action('woocommerce_product_options_downloads', [$this, 'woocommerce_product_options_downloads']);
        /// Ajout de champs taxe
        add_action('woocommerce_product_options_tax', [$this, 'woocommerce_product_options_tax']);
        /// Ajout de champs additionnels
        add_action('woocommerce_product_options_general_product_data', [$this, 'woocommerce_product_options_general_product_data']);

        /// Onglet Inventaire
        /// @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/views/html-product-data-inventory.php
        //// Ajout de champs sku/UGS (indentification produit)
        add_action('woocommerce_product_options_sku', [$this, 'woocommerce_product_options_sku']);
        //// Ajout de champs gestion de stock (si actif)
        add_action('woocommerce_product_options_stock_status', [$this, 'woocommerce_product_options_stock_status']);
        //// Ajout de champs Vente individuelle
        add_action('woocommerce_product_options_sold_individually', [$this, 'woocommerce_product_options_sold_individually']);
        /// Ajout de champs additionnels
        add_action('woocommerce_product_options_inventory_product_data', [$this, 'woocommerce_product_options_inventory_product_data']);
    }

    /**
     * Affichage de l'écran courant.
     *
     * @return void
     */
    public function current_screen($current_screen)
    {
        // Bypass
        if (!is_a($current_screen, 'WP_Screen'))
            return;
        if ($current_screen->id !== 'product')
            return;

        foreach ($this->metadatas() as $meta => $single) :
            if (is_numeric($meta)) :
                $meta = (string) $single;
                $single = true;
            endif;

            post_type()->post_meta()->register($current_screen->id, $meta, $single);
        endforeach;
    }

    /**
     * Déclaration des métadonnées à enregistrer.
     *
     * @return array
     */
    public function metadatas()
    {
        return [];
    }

    /**
     * DONNEES PRODUIT.
     */
    /**
     * Modification des onglets.
     *
     * @param array $tabs Onglets existants.
     *
     * @return array
     */
    public function woocommerce_product_data_tabs($tabs)
    {
        return $tabs;
    }

    /**
     * Ajout d'onglets personnalisés.
     */
    public function woocommerce_product_write_panel_tabs()
    {
    }

    /**
     * Ajout de panneaux d'édition personnalisés.
     */
    public function woocommerce_product_data_panels()
    {
    }

    /**
     * ONGLET GENERAL.
     */
    /**
     * Ajout de champs - PRIX.
     */
    public function woocommerce_product_options_pricing()
    {
    }

    /**
     * Ajout de champs - TÉLÉCHARGEMENT.
     */
    public function woocommerce_product_options_downloads()
    {
    }

    /**
     * Ajout de champs - TAXE.
     */
    public function woocommerce_product_options_tax()
    {
    }

    /**
     * Ajout de champs - ADDITIONNELS.
     */
    public function woocommerce_product_options_general_product_data()
    {
    }

    /**
     * ONGLET INVENTAIRE.
     */
    /**
     * Ajout de champs - IDENTIFICATION PRODUIT.
     */
    public function woocommerce_product_options_sku()
    {
    }

    /**
     * Ajout de champs - GESTION DE STOCK.
     */
    public function woocommerce_product_options_stock_status()
    {
    }

    /**
     * Ajout de champs - VENTE INDIVIDUELLE.
     */
    public function woocommerce_product_options_sold_individually()
    {
    }

    /**
     * Ajout de champs - ADDITIONNELS.
     */
    public function woocommerce_product_options_inventory_product_data()
    {
    }
}