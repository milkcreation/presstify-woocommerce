<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\View\{ViewEngine, ViewController};

interface Woocommerce extends WoocommerceResolverTrait
{
    /**
     * Instance de traitement des formulaires.
     *
     * @return Form
     */
    public function form(): ?Form;

    /**
     * Récupération de l'instance du conteneur d'injection de déépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Instance du gestionnaire de multi-magasins ou instance d'un magasin selon son nom de qualification.
     *
     * @param string|null $name Nom de qualification du magasin.
     *
     * @return Multistore|StoreFactory|object
     */
    public function multistore(?string $name = null): ?object;

    /**
     * Instance de gestion de produit.
     *
     * @return Product
     */
    public function product(): ?Product;

    /**
     * Récupération d'une instance fournie par le conteneur d'injection de dépendance.
     */
    public function resolve($alias);

    /**
     * Intance du gestionnaire de routage.
     *
     * @return Routing
     */
    public function routing(): ?Routing;

    /**
     * Instance du gestionnaire de shortcode.
     *
     * @return Shortcodes
     */
    public function shortcode(): ?Shortcodes;

    /**
     * Instance du controleur de gabarit d'affichage.
     *
     * @param null|string Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en arguments au gabarit.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = []): ?object;
}