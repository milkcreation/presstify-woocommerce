<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

interface Product extends  WoocommerceResolverTrait
{
    /**
     * Récupération d'un produit.
     *
     * @param null|int
     *
     * @return null|QueryProduct
     */
    public function get(?int $product_id = null): ?QueryProduct;

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @param QueryProduct $product
     *
     * @return array
     */
    public function getDatas(QueryProduct $product): array;

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @param QueryProduct $product
     *
     * @return array
     */
    public function getInfos(QueryProduct $product): array;
}