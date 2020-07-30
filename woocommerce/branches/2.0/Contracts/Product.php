<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use WC_Product, WP_Post, WP_Query;

interface Product extends WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération d'un produit.
     *
     * @param int|string|WC_Product|WP_Post|null $product
     *
     * @return null|QueryProduct
     */
    public function get($product = null): ?QueryProduct;

    /**
     * Récupération d'une liste des instances de commande courantes|selon une requête WP_Query|selon une liste d'arguments.
     *
     * @param WP_Query|array|null $query
     *
     * @return QueryProduct[]|array
     */
    public function fetch($query = null): array;
}