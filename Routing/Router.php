<?php

namespace tiFy\Plugins\Woocommerce\Routing;

use tiFy\Contracts\Routing\RouteGroup;

class Router
{
    public function __construct()
    {
        add_action(
            'after_setup_tify',
            function () {
                router()->register(
                    'shop',
                    [
                        'path' => '/boutique/{cat_name}[/{child_name}[/{product_name}]]',
                        'cb'   => new Shop()
                    ]
                );
                router()->group(
                    '/mon-compte',
                    function (RouteGroup $router) {
                        /**
                         * Mon compte
                         */
                        $router->get(
                            '/',
                            function () {
                                return $this->viewer(
                                    'wc.myaccount::dashboard',
                                    ['current_user' => wp_get_current_user()]
                                );
                            }
                        );

                        /**
                         * Commandes
                         */
                        $router->get(
                            get_option('woocommerce_myaccount_orders_endpoint'),
                            function () {
                                $current_page = empty($current_page) ? 1 : absint($current_page);
                                $customer_orders = wc_get_orders(
                                    apply_filters(
                                        'woocommerce_my_account_my_orders_query',
                                        [
                                            'customer' => get_current_user_id(),
                                            'page'     => $current_page,
                                            'paginate' => true,
                                        ]
                                    )
                                );

                                $columns = apply_filters(
                                    'woocommerce_account_orders_columns', [
                                        'order-number'  => __('Order', 'woocommerce'),
                                        'order-date'    => __('Date', 'woocommerce'),
                                        'order-status'  => __('Status', 'woocommerce'),
                                        'order-total'   => __('Total', 'woocommerce'),
                                        'order-again'   => __('Commander', 'theme'),
                                        'order-actions' => __('Voir la commande', 'theme'),
                                    ]
                                );

                                return $this->viewer(
                                    'wc.myaccount::orders',
                                    [
                                        'current_page'    => absint($current_page),
                                        'customer_orders' => $customer_orders,
                                        'has_orders'      => 0 < $customer_orders->total,
                                        'columns'         => $columns
                                    ]
                                );
                            }
                        );
                    }
                );
            }
        );

    }
}