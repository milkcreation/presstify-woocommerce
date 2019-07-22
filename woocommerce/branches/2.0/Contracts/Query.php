<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use WP_Query;

interface Query extends WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Court-circuitage par défaut de la requête de récupération.
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     * @param string $tag Nom du contexte.
     *
     * @return void
     */
    public function get_posts(WP_Query &$wpQuery, $tag): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_account_page()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_account_page(WP_Query &$wpQuery): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_cart()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_cart(WP_Query &$wpQuery): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_checkout()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_checkout(WP_Query &$wpQuery): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_product()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_product(WP_Query &$wpQuery): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_product_category()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_product_category(WP_Query &$wpQuery): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_product_tag()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_product_tag(WP_Query &$wpQuery): void;

    /**
     * Court-circuitage de la requête de récupération de contexte is_shop()
     *
     * @param WP_Query $wpQuery Instance globale de la requête WordPress.
     *
     * @return void
     */
    public function get_posts_shop(WP_Query &$wpQuery): void;
}