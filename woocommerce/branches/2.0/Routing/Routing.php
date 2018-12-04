<?php

namespace tiFy\Plugins\Woocommerce\Routing;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Routing as RoutingContract;

/**
 * IDENTIFIANTS DE CONTEXTE
 * @see Woocommerce/includes/wc-conditional-functions.php
 * @see https://docs.woocommerce.com/document/conditional-tags/
 */
class Routing extends ParamsBag implements RoutingContract
{
    /**
     * Liste des routes WooCommerce existantes autorisées.
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
     * Liste des routes personnalisées greffées à WooCommerce.
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
     * @param array $routes Liste des routes personnalisées.
     */
    public function __construct($customWcRoutes = [])
    {
        parent::__construct($customWcRoutes);

        foreach ($this->all() as $routeName => $routeAttrs) :
            $this->addCustom($routeName, $routeAttrs);
        endforeach;

        $this->routes = array_merge($this->wcRoutes, $this->getCustomRoutes());

        $this->registerCustomRoutesAdmin();
    }

    /**
     * Ajout d'une route personnalisée.
     *
     * @param string $route Nom de la route.
     * @param array $routeAttrs Attributs de la route personnalisée.
     *
     * @return array|bool
     */
    public function addCustom($routeName, $routeAttrs = [])
    {
        if (!$this->isCustom($routeName)) :
            return $this->customWcRoutes[$routeName] = $routeAttrs;
        endif;

        return false;
    }

    /**
     * Vérification de l'existence d'une route personnalisée.
     *
     * @param string $route Nom de la route
     *
     * @return bool
     */
    public function isCustom($route)
    {
        return in_array($route, $this->getCustomRoutes());
    }

    /**
     * Récupération des routes personnalisées.
     *
     * @return array
     */
    public function getCustomRoutes()
    {
        return array_keys($this->customWcRoutes);
    }

    /**
     * Récupération de la liste des identifiants de contextes autorisés.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Vérification d'une route.
     *
     * @param string $route Nom de la route.
     *
     * @return bool
     */
    public function is($route)
    {
        return in_array($route, $this->getRoutes());
    }

    /**
     * Récupération des contexte de la page courante
     *
     * @return array tag de la page courante | false si la page courante n'est pas un contexte Woocommerce
     */
    final public static function current()
    {
        $context = [];
        foreach (self::getAll() as $tag) :
            if (is_callable('is_' . $tag) && @ call_user_func('is_' . $tag)) :
                array_push($context, $tag);
            endif;
        endforeach;

        if (empty($context))
            return false;

        return $context;
    }

    /**
     * Vérifie si la page courante correspond au contexte
     *
     * @return bool
     */
    final public static function isCurrent($context)
    {
        if ($current = self::current())
            return in_array($context, (array)$current);
    }


    /**
     * Déclaration des interfaces d'administration pour les routes personnalisées.
     *
     * @return void
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
                                'desc'  => __('Ces pages doivent être définies pour le bon fonctionnement de votre boutique WooCommerce.', 'tify'),
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

    /**
     * Vérification de l'existence d'une route administrable.
     *
     * @return bool
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
}