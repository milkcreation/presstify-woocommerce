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
     * Instance de la boutique associée.
     * @var StoreFactory|null|false
     */
    protected $store;


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
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('store', $this->store());

        return parent::render();
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
        $this->store = $store;

        return $this;
    }

    /**
     * Récupération de l'instance du magasin associé.
     *
     * @return StoreFactory|null
     */
    public function store(): ?StoreFactory
    {
        if (is_null($this->store)) {
            $store = $this->get('store');

            $this->store = $store instanceof StoreFactory ? $store : false;
        }

        return $this->store ?: null;
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