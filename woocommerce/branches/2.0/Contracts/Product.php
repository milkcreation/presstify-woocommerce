<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

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
     * @param null|int
     *
     * @return null|QueryProduct
     */
    public function get(?int $product_id = null): ?QueryProduct;
}