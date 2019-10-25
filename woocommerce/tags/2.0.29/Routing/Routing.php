<?php

namespace tiFy\Plugins\Woocommerce\Routing;

use tiFy\Plugins\Woocommerce\Contracts\Routing as RoutingContract;
use tiFy\Support\ParamsBag;

/**
 * @see Woocommerce/includes/wc-conditional-functions.php
 * @see https://docs.woocommerce.com/document/conditional-tags/
 */
class Routing extends ParamsBag implements RoutingContract
{
    /**
     * Liste des routes Woocommerce existantes autorisées.
     * @see https://docs.woocommerce.com/document/conditional-tags/
     * @var array
     */
    protected $wcRoutes = [
        'woocommerce',
        'shop',
        'product_taxonomy',
        'product_category',
        'product_tag',
        'product',
        'cart',
        'checkout',
        'checkout_pay_page',
        'account_page',
        'view_order_page',
        'edit_account_page',
        'add_payment_method_page',
        'lost_password_page'
    ];

    /**
     * Liste des routes personnalisées greffées à Woocommerce.
     * @var array
     */
    protected $customWcRoutes = [];

    /**
     * Liste des routes de tout type.
     * @var array
     */
    protected $routes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $customWcRoutes Liste des routes personnalisées.
     */
    public function __construct($customWcRoutes = [])
    {
        $this->set($customWcRoutes)->parse();

        foreach ($this->all() as $customWcRouteName => $customWcRouteAttrs) {
            $this->addCustom($customWcRouteName, $customWcRouteAttrs);
        }

        $this->routes = array_merge($this->wcRoutes, $this->getCustomRoutes());

        $this->registerCustomRoutesAdmin();
        $this->bindToWoocommerce();
    }

    /**
     * {@inheritdoc}
     */
    public function addCustom($routeName, $routeAttrs = [])
    {
        if (!$this->isCustom($routeName)) :
            return $this->customWcRoutes[$routeName] = $routeAttrs;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function customRouteAdminExists()
    {
        foreach ($this->customWcRoutes as $routeName => $routeAttrs) :
            if (!empty($routeAttrs['admin']) && $routeAttrs['admin']) :
                return true;
            endif;
        endforeach;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($route)
    {
        return in_array($route, $this->getRoutes());
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomRoutes()
    {
        return array_keys($this->customWcRoutes);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function is($route)
    {
        if (!$this->exists($route)) :
            return false;
        endif;

        $callable = (is_callable('is_' . $route) && @call_user_func('is_' . $route)) || (method_exists($this, 'is_' . $route) && @call_user_func([$this, 'is_' . $route]));

        if ($this->isCustom($route)) :
            return $callable || ((int)get_query_var('page_id') === wc_get_page_id($route));
        else :
            return $callable;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function isCustom($route)
    {
        return in_array($route, $this->getCustomRoutes());
    }

    /**
     * {@inheritdoc}
     */
    public function bindToWoocommerce()
    {
        add_filter(
            'is_woocommerce',
            function ($cond) {
                if (!$customWcRoutes = $this->getCustomRoutes()) :
                    return $cond;
                endif;

                foreach ($customWcRoutes as $customWcRoute) :
                    $cond = $cond || $this->is($customWcRoute);
                endforeach;

                return $cond;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomRoutesAdmin()
    {
        if ($this->customRouteAdminExists()) :
            add_filter(
                'woocommerce_settings_pages',
                function ($items) {
                    if (!$this->customWcRoutes) :
                        return $items;
                    endif;
                    $_items = [];
                    foreach ($items as $index => $item) :
                        if (($item['type'] === 'sectionend') && ($item['id'] === 'advanced_page_options')) :
                            $_items[$index] = $item;
                            $_items['custom-wc-routes-start'] = [
                                'title' => __('Pages personnalisées', 'tify'),
                                'desc'  => __('Ces pages doivent être définies pour le bon fonctionnement de votre boutique Woocommerce.', 'tify'),
                                'type'  => 'title',
                                'id'    => 'advanced_custom_page_options'
                            ];
                            foreach ($this->customWcRoutes as $routeName => $routeAttrs) :
                                if (!empty($routeAttrs['admin']) && $routeAttrs['admin']) :
                                    $_items["custom-wc-routes-$routeName"] = [
                                        'title'    => $routeAttrs['admin']['title'] ?? __('Page personnalisée', 'tify'),
                                        'desc'     => $routeAttrs['admin']['desc'] ?? false,
                                        'id'       => "woocommerce_{$routeName}_page_id",
                                        'type'     => 'single_select_page',
                                        'default'  => '',
                                        'class'    => 'wc-enhanced-select-nostd',
                                        'css'      => 'min-width:300px;',
                                        'desc_tip' => !empty($routeAttrs['admin']['desc'])
                                    ];
                                endif;
                            endforeach;

                            $_items['custom-wc-routes-end'] = [
                                'type' => 'sectionend',
                                'id'   => 'advanced_custom_page_options'
                            ];
                        else :
                            $_items[$index] = $item;
                        endif;
                    endforeach;

                    return $items = $_items;
                }
            );
        endif;
    }
}