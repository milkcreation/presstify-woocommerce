<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface Routing extends ParamsBag
{
    /**
     * Ajout d'une route personnalisée.
     *
     * @param string $routeName Nom de la route.
     * @param array $routeAttrs Attributs de la route personnalisée.
     *
     * @return array|bool
     */
    public function addCustom($routeName, $routeAttrs = []);

    /**
     * Vérification de l'existence d'une route administrable.
     *
     * @return bool
     */
    public function customRouteAdminExists();

    /**
     * Vérification de l'existence d'une route.
     *
     * @param string $route Nom de la route.
     *
     * @return bool
     */
    public function exists($route);

    /**
     * Récupération des routes personnalisées.
     *
     * @return array
     */
    public function getCustomRoutes();

    /**
     * Récupération des routes.
     *
     * @return array
     */
    public function getRoutes();

    /**
     * Vérification de l'affichage de la page liée à la route courante.
     *
     * @param string $route Nom de la route.
     *
     * @return bool
     */
    public function is($route);

    /**
     * Vérification de l'existence d'une route personnalisée.
     *
     * @param string $route Nom de la route
     *
     * @return bool
     */
    public function isCustom($route);

    /**
     * Branchement des routes personnalisées à l'environnement Woocommerce.
     *
     * @return void
     */
    public function bindToWoocommerce();

    /**
     * Déclaration des interfaces d'administration pour les routes personnalisées.
     *
     * @return void
     */
    public function registerCustomRoutesAdmin();
}