<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Store;

use tiFy\Plugins\Woocommerce\Contracts\{
    Stores,
    StoreFactory,
    Woocommerce};
use tiFy\Metabox\MetaboxDriver;

class AbstractStoreMetabox extends MetaboxDriver
{
    /**
     * Récupération de l'instance du magasin associé.
     *
     * @return $this
     */
    public function setStore(StoreFactory $store): self
    {
        $this->set('store', $store);

        return $this;
    }

    /**
     * Récupération de l'instance du magasin associé.
     *
     * @return StoreFactory
     */
    public function store(): StoreFactory
    {
        return $this->get('store');
    }

    /**
     * Récupération du gestionnaire de magasins.
     *
     * @return Stores
     */
    public function stores(): Stores
    {
        return $this->store()->stores();
    }

    /**
     * Récupération du gestionnaire de plugin Woocommerce.
     *
     * @return Woocommerce
     */
    public function woocommerce(): Woocommerce
    {
        return $this->stores()->manager();
    }
}