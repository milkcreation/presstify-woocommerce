<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface MetaboxProduct extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Déclaration des métadonnées à enregistrer.
     *
     * @return array
     */
    public function metadatas(): array;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): MetaboxProduct;

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
    public function woocommerce_product_data_tabs(array $tabs): array;

    /**
     * Ajout d'onglets personnalisés.
     *
     * @return void
     */
    public function woocommerce_product_write_panel_tabs(): void;

    /**
     * Ajout de panneaux d'édition personnalisés.
     *
     * @return void
     */
    public function woocommerce_product_data_panels(): void;

    /**
     * ONGLET GENERAL.
     */
    /**
     * Ajout de champs - PRIX.
     *
     * @return void
     */
    public function woocommerce_product_options_pricing(): void;

    /**
     * Ajout de champs - TÉLÉCHARGEMENT.
     *
     * @return void
     */
    public function woocommerce_product_options_downloads(): void;

    /**
     * Ajout de champs - TAXE.
     *
     * @return void
     */
    public function woocommerce_product_options_tax(): void;

    /**
     * Ajout de champs - ADDITIONNELS.
     *
     * @return void
     */
    public function woocommerce_product_options_general_product_data(): void;

    /**
     * ONGLET INVENTAIRE.
     */
    /**
     * Ajout de champs - IDENTIFICATION PRODUIT.
     *
     * @return void
     */
    public function woocommerce_product_options_sku(): void;

    /**
     * Ajout de champs - GESTION DE STOCK.
     *
     * @return void
     */
    public function woocommerce_product_options_stock_status(): void;

    /**
     * Ajout de champs - VENTE INDIVIDUELLE.
     *
     * @return void
     */
    public function woocommerce_product_options_sold_individually(): void;

    /**
     * Ajout de champs - ADDITIONNELS.
     *
     * @return void
     */
    public function woocommerce_product_options_inventory_product_data(): void;
}