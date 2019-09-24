<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\Collection;

interface Stores extends Collection, WoocommerceAwareTrait
{
    /**
     * {@inheritDoc}
     *
     * @return StoreFactory[]
     */
    public function all();

    /**
     * Récupération du magasin associé à la page d'affichage courante.
     *
     * @return StoreFactory|null
     */
    public function getCurrent(): ?StoreFactory;

    /**
     * Récupération de la liste des identifiants de qualification des pages associées aux magasins.
     *
     * @return int[]
     */
    public function getPageIds(): array;

    /**
     * Récupération de la liste des identifiants de qualification des categories de produits associées aux magasins.
     *
     * @return int[]
     */
    public function getProductCatIds(): array;
}