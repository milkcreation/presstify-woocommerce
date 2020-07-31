<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\{Contracts\Query as QueryContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;
use WP_Query;

/**
 * REQUETE DE RECUPERATION DES ELEMENTS DE PAGE
 * @see Woocommerce/includes/class-wc-query.php
 */
class Query extends ParamsBag implements QueryContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('pre_get_posts', function (WP_Query &$wpQuery) {
            if (!is_admin() && $wpQuery->is_main_query()) {
                foreach ($this->manager->routing()->getRoutes() as $route) {
                    if ($this->manager->routing()->is($route)) {
                        call_user_func_array([$this, 'get_posts'], [&$wpQuery, $route]);
                        if (method_exists($this, 'get_posts_' . $route)) {
                            call_user_func_array([$this, 'get_posts_' . $route], [&$wpQuery]);
                        }
                    }
                }
            }
        }, 99);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function get_posts(WP_Query &$wpQuery, $tag): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_account_page(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_cart(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_checkout(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_product(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_product_category(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_product_tag(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function get_posts_shop(WP_Query &$wpQuery): void {}

    /**
     * @inheritDoc
     */
    public function parse(): QueryContract
    {
        parent::parse();

        return $this;
    }
}