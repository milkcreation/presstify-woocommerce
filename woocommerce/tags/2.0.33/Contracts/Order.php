<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use WP_Query, WP_Post;

interface Order extends WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération d'une commande.
     *
     * @param int|string|WP_Post|null $order
     *
     * @return QueryOrder|null
     */
    public function get($order = null): ?QueryOrder;

    /**
     * Récupération d'une liste des instances de commande courantes|selon une requête WP_Query|selon une liste d'arguments.
     *
     * @param WP_Query|array|null $query
     *
     * @return QueryOrder[]|array
     */
    public function fetch($query = null): array;
}