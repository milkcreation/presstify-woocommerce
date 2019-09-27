<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Metabox;

use tiFy\Plugins\Woocommerce\{Contracts\MetaboxProduct as MetaboxProductContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;
use WP_Screen;

/**
 * @see /wp-content/plugins/woocommerce/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
 * @see http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
 */
class MetaboxProduct extends ParamsBag implements MetaboxProductContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('current_screen', function (WP_Screen $wp_screen) : void {
            if ($wp_screen->id === 'product') {
                foreach ($this->metadatas() as $meta => $single) {
                    if (is_numeric($meta)) {
                        $meta = (string)$single;
                        $single = true;
                    }
                    post_type()->post_meta()->register('product', $meta, $single);
                }
            }
        });

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

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function metadatas(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxProductContract
    {
        parent::parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function woocommerce_product_data_tabs(array $tabs): array
    {
        return $tabs;
    }

    /**
     * @inheritDoc
     */
    public function woocommerce_product_write_panel_tabs(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_data_panels(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_pricing(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_downloads(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_tax(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_general_product_data(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_sku(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_stock_status(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_sold_individually(): void {}

    /**
     * @inheritDoc
     */
    public function woocommerce_product_options_inventory_product_data(): void {}
}