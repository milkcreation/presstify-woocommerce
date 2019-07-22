<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Woocommerce\Contracts\{
    Form,
    Product,
    Routing,
    Multistore,
    Shortcodes,
    Woocommerce as WoocommerceContract};

/**
 * Class Woocommerce
 *
 * @desc Extension PresstiFy de court-circuitage et de fonctionnalités complémentaires woocommerce.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Woocommerce
 * @version 2.0.17
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
    public function multistore(?string $name = null): ?object
    {
        /** @var Multistore $multistore */
        $multistore = $this->resolve('multistore');

        return is_null($name) ? $multistore : $multistore->get($name);
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
    public function resolve($alias)
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
    public function shortcode(): ?Shortcodes
    {
        return $this->resolve('shortcodes');
    }

    /**
     * @inheritDoc
     */
    public function viewer($view = null, $data = []): ?object
    {
        /** @var ViewEngine $viewer */
        $viewer = $this->resolve('viewer');

        if (func_num_args() === 0) {
            return $viewer;
        }

        return $viewer->make("_override::{$view}", $data);
    }
}
