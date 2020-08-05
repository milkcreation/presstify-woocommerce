<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use tiFy\Contracts\View\Engine;
use tiFy\Contracts\Container\Container;
use tiFy\Plugins\Woocommerce\Contracts\{
    Cart,
    Checkout,
    Form,
    Order,
    Product,
    ProductCat,
    Routing,
    Stores,
    Shortcodes,
    Woocommerce as WoocommerceContract
};

/**
 * @desc Extension PresstiFy de court-circuitage et de fonctionnalités complémentaires woocommerce.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Woocommerce
 * @version 2.0.35
 *
 * @see https://docs.woocommerce.com/wc-apidocs/index.html
 *
 * USAGE :
 * Activation
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Woocommerce\WoocommerceServiceProvider à la liste des fournisseurs de
 * services.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\Woocommerce\WoocommerceServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          WoocommerceServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier woocommerce.php
 * @see /vendor/presstify-plugins/woocommerce/Resources/config/woocommerce.php
 */
class Woocommerce implements WoocommerceContract
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container $container Instance du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function cart(): ?Cart
    {
        return $this->resolve('cart');
    }

    /**
     * @inheritDoc
     */
    public function checkout(): ?Checkout
    {
        return $this->resolve('checkout');
    }

    /**
     * @inheritDoc
     */
    public function form(): ?Form
    {
        return $this->resolve('form');
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function order(): ?Order
    {
        return $this->resolve('order');
    }

    /**
     * @inheritDoc
     */
    public function product(): ?Product
    {
        return $this->resolve('product');
    }

    /**
     * @inheritDoc
     */
    public function productCat(): ?ProductCat
    {
        return $this->resolve('product-cat');
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $alias)
    {
        return $this->getContainer() ? $this->getContainer()->get("woocommerce.{$alias}") : null;
    }

    /**
     * @inheritDoc
     */
    public function routing(): ?Routing
    {
        return $this->resolve('routing');
    }

    /**
     * @inheritDoc
     */
    public function shortcodes(): ?Shortcodes
    {
        return $this->resolve('shortcodes');
    }

    /**
     * @inheritDoc
     */
    public function store(?string $name = null): ?object
    {
        /** @var Stores $stores */
        $stores = $this->resolve('stores');

        return is_null($name) ? $stores : $stores->get($name);
    }

    /**
     * @inheritDoc
     */
    public function viewer($view = null, $data = [])
    {
        /** @var Engine $viewer */
        $viewer = $this->resolve('viewer');

        if (func_num_args() === 0) {
            return $viewer;
        }

        return $viewer->render($view, $data);
    }
}
