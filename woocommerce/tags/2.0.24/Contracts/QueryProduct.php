<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\{Collection, ParamsBag};
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
     * Récupération d'attributs associés à un produit.
     *
     * @param string $key Clé d'indice de l'information à retrouver. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut
     *
     * @return ParamsBag|mixed
     */
    public function getAttributes(?string $key = null, $default = null);

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @return array
     */
    public function getDatas(): array;

    /**
     * Récupération du prix maximum.
     *
     * @param boolean $with_tax Indicateur d'inclusion de la taxe.
     *
     * @return float
     */
    public function getMaxPrice(?bool $with_tax = null): float;

    /**
     * Récupération du prix minimum.
     *
     * @param boolean $with_tax Indicateur d'inclusion de la taxe.
     *
     * @return float
     */
    public function getMinPrice(?bool $with_tax = null): float;

    /**
     * Récupération de l'instance du produit parent d'une variation.
     *
     * @return QueryProduct|null
     */
    public function getParent(): ?QueryPost;

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
     * Récupération de l'unité de gestion de stock (UGS aka SKU).
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Récupération du prix d'une variation.
     *
     * @param bool $min
     *
     * @return float
     */
    public function getVariationPrice($min = true): float;

    /**
     * Récupération de la liste des variations de prix d'un produit variable.
     *
     * @return Collection|null
     */
    public function getVariationPrices(): ?Collection;

    /**
     * Récupération des instances de toutes les variations d'un produit variable.
     *
     * @return QueryProducts|QueryProduct[]|null
     */
    public function getVariations(): ?QueryProducts;

    /**
     * Récupération des instances de variations disponibles d'un produit variable.
     *
     * @return QueryProducts|QueryProduct[]|null
     */
    public function getVariationsAvailable(): ?QueryProducts;

    /**
     * Récupération de l'instance du produit associé.
     *
     * @return WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    public function getWcProduct(): WC_Product;

    /**
     * Vérifie l'existance de variations pour un produit variable.
     * {@internal Le produit doit être variable et le nombre de variation supérieur à 1.}
     *
     * @return false
     */
    public function hasVariation(): bool;

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