<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Wp\QueryPost;
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;
use WP_Post;

interface QueryProduct extends ParamsBag
{
    /**
     * Récupération d'une instance basée sur le produit global courant.
     *
     * @return static
     */
    public static function createFromGlobal() : ?QueryProduct;

    /**
     * Récupération d'une instance basée sur l'identifiant de qualification d'un produit.
     *
     * @param int $product_id
     *
     * @return static
     */
    public static function createFromId($product_id) : ?QueryProduct;

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Récupération de l'instance du post tiFy associé.
     *
     * @return null|QueryPost
     */
    public function getQueryPost(): ?QueryPost;

    /**
     * Récupération de l'instance du produit associé.
     *
     * @return WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    public function getProduct(): WC_Product;

    /**
     * Récupération de l'instance tiFy du produit parent.
     * {@internal Opérant uniquement pour les variation de produit}
     *
     * @return null|QueryProduct
     */
    public function getProductParent(): ?QueryProduct;

    /**
     * Récupération de l'instance du post Wordpress associé.
     *
     * @return null|WP_Post
     */
    public function getPost(): ?WP_Post;

    /**
     * Récupération du prix taxe incluse.
     *
     * @param array $args Liste des arguments optionnels.
     *
     * @return float
     */
    public function getPriceIncludingTax($args = []): float;

    /**
     * Récupération du prix taxe exclue.
     *
     * @param array $args Liste des arguments optionnels.
     *
     * @return float
     */
    public function getPriceExcludingTax($args = []): float;

    /**
     * Récupération de la liste des variations associée au produit.
     *
     * @return array|QueryProducts|QueryProduct[]
     */
    public function getVariations();

    /**
     * Vérifie si le produit associé est un produit simple.
     *
     * @return boolean
     */
    public function isSimple(): bool;

    /**
     * Vérifie si le produit associé est un produit variable.
     *
     * @return boolean
     */
    public function isVariable(): bool;

    /**
     * Vérifie si le produit associé est une variation.
     *
     * @return boolean
     */
    public function isVariation(): bool;
}