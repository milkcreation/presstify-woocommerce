<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Container\Container;
use WC_Product;

interface Woocommerce extends WoocommerceResolverTrait
{
    /**
     * Instance du gestionnaire de panier.
     *
     * @return Cart|object|null
     */
    public function cart(): ?Cart;

    /**
     * Instance du gestionnaire de paiement.
     *
     * @return Checkout|object|null
     */
    public function checkout(): ?Checkout;

    /**
     * Instance de traitement des formulaires.
     *
     * @return Form|object|null
     */
    public function form(): ?Form;

    /**
     * Récupération de l'instance du conteneur d'injection de déépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Instance de gestionnaire de commandes.
     *
     * @return Order|object|null
     */
    public function order(): ?Order;

    /**
     * Instance du gestionnaire de produits.
     *
     * @return Product|object|null
     */
    public function product(): ?Product;

    /**
     * Instance du gestionnaire de catégories de produits.
     *
     * @return ProductCat|object|null
     */
    public function productCat(): ?ProductCat;

    /**
     * Récupération d'une instance fournie par le conteneur d'injection de dépendance.
     *
     * @param string $alias Alias de qualification du service à fournir.
     *
     * @return object|null
     */
    public function resolve(string $alias);

    /**
     * Intance du gestionnaire de routage.
     *
     * @return Routing|object|null
     */
    public function routing(): ?Routing;

    /**
     * Instance du gestionnaire de shortcode.
     *
     * @return Shortcodes|object|null
     */
    public function shortcodes(): ?Shortcodes;

    /**
     * Instance du gestionnaire de magasins ou instance d'un magasin selon son nom de qualification.
     *
     * @param string|null $name Nom de qualification du magasin.
     *
     * @return Stores|StoreFactory|object
     */
    public function store(?string $name = null): ?object;

    /**
     * Instance du controleur de gabarit d'affichage.
     *
     * @param null|string Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en arguments au gabarit.
     *
     * @return object|string
     */
    public function viewer($view = null, $data = []);
}