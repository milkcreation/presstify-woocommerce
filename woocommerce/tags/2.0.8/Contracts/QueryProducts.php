<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\Collection;
use WP_Query;

interface QueryProducts extends Collection
{
    /**
     * Récupération d'une instance basée sur une liste des arguments.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return static
     */
    public static function createFromArgs($args = []): QueryProducts;

    /**
     * Récupération d'une instance basée sur la requête globale.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @return static
     */
    public static function createFromGlobals(): QueryProducts;

    /**
     * Récupération d'une instance basée sur une liste d'identifiant de qualification de produits.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param $ids
     *
     * @return static
     */
    public static function createFromIds(array $ids): QueryProducts;

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getIds() : array;

    /**
     * Récupération de l'instance de la requête Wordpress de récupération des produits.
     *
     * @return null|WP_Query
     */
    public function WpQuery() : WP_Query;
}