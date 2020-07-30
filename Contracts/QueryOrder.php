<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Wordpress\Contracts\Query\QueryPost;
use WC_Order;

/**
 * @mixin WC_Order
 */
interface QueryOrder extends QueryPost
{
    /**
     * Délégation d'appel des méthodes de l'objet Woocommerce.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters = []);

    /**
     * Récupération de l'identifiant de qualification du client associé.
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Récupération du statut court.
     *
     * @return string
     */
    public function getShortStatus(): string;

    /**
     * Récupération de l'instance de la commande Woocommerce associée.
     *
     * @return WC_Order|null
     */
    public function getWcOrder(): ?WC_Order;

    /**
     * Vérifie si l'utilisateur courant est le client associé.
     *
     * @return bool
     */
    public function isCustomer(): bool;

    /**
     * Vérification d'existance d'un statut court.
     *
     * @param string|array $statuses
     *
     * @return bool
     */
    public function hasShortStatus($statuses): bool;
}