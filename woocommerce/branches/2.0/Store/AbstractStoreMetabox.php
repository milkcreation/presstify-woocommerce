<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Store;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Plugins\Woocommerce\Contracts\Stores;
use tiFy\Plugins\Woocommerce\Contracts\StoreFactory;
use tiFy\Plugins\Woocommerce\Contracts\Woocommerce;
use tiFy\Metabox\MetaboxDriver;

class AbstractStoreMetabox extends MetaboxDriver
{
    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parse(): ?MetaboxDriverContract
    {
        $this->attributes = array_merge(
            $this->defaults(), $this->metabox()->config("driver.{$this->getAlias()}", []), $this->attributes
        );

        $this->params(array_merge($this->defaultParams(), $this->get('params', [])));

        return $this;
    }

    /**
     * Récupération de l'instance du magasin associé.
     *
     * @param StoreFactory $store
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