<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Store;

use tiFy\Plugins\Woocommerce\Contracts\{Stores as StoresContracts, StoreFactory as StoreFactoryContract};
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use tiFy\Support\Collection;

class Stores extends Collection implements StoresContracts
{
    use WoocommerceAwareTrait;

    /**
     * Instance du magasin courant.
     * @var StoreFactoryContract|null
     */
    protected $current;

    /**
     * Instance des magasins déclarés.
     * @var StoreFactoryContract[]
     */
    protected $items = [];

    /**
     * Liste des identifiants de qualification des pages associées aux magasins.
     * @var int[]
     */
    protected $pageIds;

    /**
     * Liste des identifiants de qualification des catégories de produits associées aux magasins.
     * @var int[]
     */
    protected $productCatIds;

    /**
     * {@inheritDoc}
     *
     * @return StoreFactoryContract[]
     */
    public function all()
    {
        return parent::all();
    }

    /**
     * @inheritDoc
     */
    public function getCurrent(): ?StoreFactoryContract
    {
        if (is_null($this->current)) {
            foreach($this->items as $store) {
                if ($store->isCurrent()) {
                    $this->current = $store;
                    break;
                }
            }
        }

        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function getPageIds(): array
    {
        if (is_null($this->pageIds)) {
            $this->pageIds = [];
            foreach($this->items as $store) {
                $this->pageIds[] = $store->getPageId();
            }
            $this->pageIds = array_unique($this->pageIds);
        }

        return $this->pageIds;
    }

    /**
     * @inheritDoc
     */
    public function getProductCatIds(): array
    {
        if (is_null($this->productCatIds)) {
            $this->productCatIds = [];
            foreach($this->items as $store) {
                $this->productCatIds[] = $store->getProductCatId();
            }
            $this->productCatIds = array_unique($this->productCatIds);
        }

        return $this->productCatIds;
    }

    /**
     * @inheritDoc
     */
    public function walk($item, $name = null)
    {
        if (!$item instanceof StoreFactoryContract) {
            $attrs = $item;
            $item = $this->manager->resolve('store-factory');
        } else {
            $attrs = [];
        }

        $this->items[$name] = $item->prepare((string)$name, $this)->set($attrs)->parse();
    }
}