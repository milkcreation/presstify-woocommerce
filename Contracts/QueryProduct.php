<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Wordpress\Contracts\QueryPost;
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;

interface QueryProduct extends QueryPost
{
    /**
     * {@inheritDoc}
     *
     * @return static|null
     */
    public static function createFromGlobal(): ?QueryPost;

    /**
     * {@inheritDoc}
     *
     * @return static|null
     */
    public static function createFromId($product_id): ?QueryPost;

    /**
     * Récupération de la liste des enfants associées au produit.
     * {@internal Valable pour un produit variable uniquement.}
     *
     * @return null|QueryProducts|QueryProduct[]
     */
    public function getChildren();

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @return array
     */
    public function getDatas(): array;

    /**
     * Récupération de la liste des informations associées à un produit.
     *
     * @return array
     */
    public function getInfos(): array;

    /**
     * Récupération du prix maximum.
     *
     * @param boolean $with_tax Indicateur d'inclusion de la taxe.
     *
     * @return float
     */
    public function getMaxPrice(bool $with_tax = true): float;

    /**
     * Récupération du prix minimum.
     *
     * @param boolean $with_tax Indicateur d'inclusion de la taxe.
     *
     * @return float
     */
    public function getMinPrice(bool $with_tax = true): float;

    /**
     * Récupération du prix taxe incluse.
     *
     * @param array $args Liste des arguments optionnels.
     *
     * @return float
     */
    public function getPriceIncludingTax(array $args = []): float;

    /**
     * Récupération du prix taxe exclue.
     *
     * @param array $args Liste des arguments optionnels.
     *
     * @return float
     */
    public function getPriceExcludingTax(array $args = []): float;

    /**
     * Récupération de l'instance du produit associé.
     *
     * @return WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    public function getWcProduct(): WC_Product;

    /**
     * Récupération de l'unité de gestion de stock (UGS aka SKU).
     *
     * @return string
     */
    public function getSku(): string;

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