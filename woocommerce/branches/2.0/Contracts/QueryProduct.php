<?php declare(strict_types=1);

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
    public static function createFromGlobal(): ?QueryProduct;

    /**
     * Récupération d'une instance basée sur l'identifiant de qualification d'un produit.
     *
     * @param int $product_id
     *
     * @return static
     */
    public static function createFromId($product_id): ?QueryProduct;

    /**
     * Récupération de la liste des enfants associées au produit.
     * {@internal Valable pour un produit variable uniquement.}
     *
     * @return null|QueryProducts|QueryProduct[]
     */
    public function getChildren();

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Récupération du prix maximum.
     *
     * @param boolean $with_tax Indicateur d'inclusion de la taxe.
     *
     * @return float
     */
    public function getMaxPrice($with_tax = true): float;

    /**
     * Récupération du prix minimum.
     *
     * @param boolean $with_tax Indicateur d'inclusion de la taxe.
     *
     * @return float
     */
    public function getMinPrice($with_tax = true): float;

    /**
     * Récupération de l'instance tiFy du produit parent.
     * {@internal Valable pour un produit variation uniquement.}
     *
     * @return null|QueryProduct|QueryPost
     */
    public function getParent();

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
     * Récupération de l'instance du produit associé.
     *
     * @return WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    public function getProduct(): WC_Product;

    /**
     * Récupération de l'instance du post tiFy associé.
     *
     * @return null|QueryPost
     */
    public function getQueryPost(): ?QueryPost;

    /**
     * Vérification d'existance d'un prix variable pour le produit.
     *
     * @return boolean
     */
    public function hasVariablePrice(): bool;

    /**
     * Vérification d'existance d'un tarif préférentiel.
     *
     * @return boolean
     */
    public function isOnSale(): bool;

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