<?php

namespace tiFy\Plugins\Woocommerce\Assets;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Assets as AssetsContract;
use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;

//use \tiFy\Plugins\Woocommerce\Form;

/**
 * CHARGEMENT DES SCRIPTS
 * - Désactivation des scripts natif de Woocommerce
 * - Chargement de script en contexte
 */
class Assets extends ParamsBag implements AssetsContract
{
    use WoocommerceResolverTrait;

    /**
     * Liste des styles déclarés par contexte.
     * @var array
     */
    protected $styles = [];

    /**
     * Liste des scripts déclarés par contexte.
     * @var array
     */
    protected $scripts = [];

    /**
     * Préfixe des scripts minifiés.
     * @var string
     */
    protected $min = '';

    /**
     * Liste des feuilles de styles WooCommerce.
     * @see wp-content/plugins/woocommerce/includes/class-wc-frontend-scripts.php
     * @var array
     */
    protected $wcStyles = [
        'woocommerce-layout'      => true,
        'woocommerce-smallscreen' => true,    // ! Dépendance woocommerce-layout
        'woocommerce-general'     => true
    ];

    /**
     * Liste des scripts WooCommerce.
     * @see wp-content/plugins/woocommerce/includes/class-wc-frontend-scripts.php
     * @var array
     */
    protected $wcScripts = [
        'wc-address-i18n'            => true,
        'wc-add-payment-method'      => true,
        'wc-cart'                    => true,
        'wc-cart-fragments'          => true,
        'wc-checkout'                => true,
        'wc-country-select'          => true,
        'wc-credit-card-form'        => true,
        'wc-add-to-cart'             => true,
        'wc-add-to-cart-variation'   => true,
        'wc-geolocation'             => true,
        'wc-lost-password'           => true,
        'wc-password-strength-meter' => true,
        'wc-single-product'          => true,
        'woocommerce'                => true
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        parent::__construct($attrs);

        $this->wcStyles = array_merge($this->wcStyles, $this->get('wc_styles', []));
        $this->wcScripts = array_merge($this->wcScripts, $this->get('wc_scripts', []));
        $this->min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        $this->registerAppAssets();
        $this->dequeueWcStyles();
        $this->dequeueWcScripts();
        $this->enqueue();
    }

    /**
     * Déclaration des styles/scripts de l'application.
     *
     * @return void
     */
    protected function registerAppAssets()
    {
        add_action(
            'init',
            function () {
                foreach ($this->routing()->getRoutes() as $route) :
                    if (file_exists(get_stylesheet_directory() . "/dist/css/wc-{$route}" . $this->min . '.css')) :
                        wp_register_style('tify_wc_' . $route, get_stylesheet_directory_uri() . "/dist/css/wc-{$route}" . $this->min . '.css');
                        $this->addStyle($route, 'tify_wc_' . $route);
                    endif;
                    if (file_exists(get_stylesheet_directory() . "/dist/js/wc-{$route}" . $this->min . ".js")) :
                        wp_register_script('tify_wc_' . $route, get_stylesheet_directory_uri() . "/dist/js/wc-{$route}" . $this->min . '.js');
                        $this->addScript($route, 'tify_wc_' . $route);
                    endif;
                endforeach;
            }
        );
    }

    /**
     * Désactivation des styles WooCommerce.
     *
     * @return void
     */
    protected function dequeueWcStyles()
    {
        add_filter(
            'woocommerce_enqueue_styles',
            function ($wcStyles) {
                foreach ($this->wcStyles as $handle => $bypass) :
                    if ($bypass) :
                        continue;
                    endif;
                    unset($wcStyles[$handle]);
                endforeach;

                return $wcStyles;
            }
        );
    }

    /**
     * Désactivation des scripts WooCommerce.
     *
     * @return void
     */
    protected function dequeueWcScripts()
    {
        add_action(
            'wp_enqueue_scripts',
            function () {
                foreach ($this->wcScripts as $handle => $bypass) :
                    if ($bypass) :
                        continue;
                    endif;
                    wp_dequeue_script($handle);
                endforeach;
            },
            25
        );
    }

    /**
     * Mise en queue des styles/scripts de l'application.
     *
     * @return void
     */
    protected function enqueue()
    {
        add_action(
            'wp_enqueue_scripts',
            function () {
                if ($this->form()->istiFySelectJsCountry() && ($this->routing()->is('checkout') || $this->routing()->is('account_page'))) :
                    wp_dequeue_script('select2');
                    wp_dequeue_style('select2');
                endif;

                $this->enqueue_scripts_before_global();
                if (file_exists(get_stylesheet_directory() . "/dist/css/wc-global" . $this->min . ".css")) :
                    wp_enqueue_style('tify_wc_global', get_stylesheet_directory_uri() . "/dist/css/wc-global" . $this->min . ".css");
                endif;
                if (file_exists(get_stylesheet_directory() . "/dist/js/wc-global" . $this->min . ".js")) :
                    wp_enqueue_script('tify_wc_global', get_stylesheet_directory_uri() . "/dist/js/wc-global" . $this->min . ".js");
                endif;
                $this->enqueue_scripts_after_global();

                foreach ($this->routing()->getRoutes() as $route) :
                    if ($this->routing()->is($route)) :
                        $this->enqueue_scripts_before($route);
                        if (is_callable([$this, 'enqueue_scripts_before_' . $route])) :
                            call_user_func([$this, 'enqueue_scripts_before_' . $route]);
                        endif;

                        if ($this->hasStyle($route)) :
                            foreach ($this->getStyles($route) as $style) :
                                wp_enqueue_style($style);
                            endforeach;
                        endif;

                        if ($this->hasScript($route)) :
                            foreach ($this->getScripts($route) as $script) :
                                wp_enqueue_script($script);
                            endforeach;
                        endif;

                        if (is_callable([$this, 'enqueue_scripts_after_' . $route])) :
                            call_user_func([$this, 'enqueue_scripts_after_' . $route]);
                        endif;
                        $this->enqueue_scripts_after($route);
                    endif;
                endforeach;
            },
            25
        );
    }

    /**
     * Déclaration d'un style dans un contexte.
     *
     * @param string $tag Nom du contexte.
     * @param string $hook Nom du style.
     *
     * @return void
     */
    public function addStyle($tag, $hook)
    {
        // Bypass
        if (!$this->routing()->exists($tag)) :
            return null;
        endif;

        if (!isset($this->styles[$tag])) :
            $this->styles[$tag] = [];
        endif;

        array_push($this->styles[$tag], $hook);
    }

    /**
     * Vérification d'existence d'un style dans un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return bool
     */
    public function hasStyle($tag)
    {
        return $this->routing()->is($tag) && !empty($this->styles[$tag]);
    }

    /**
     * Récupération des styles d'un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return array|false
     */
    public function getStyles($tag)
    {
        return $this->hasStyle($tag) ? array_unique($this->styles[$tag]) : false;
    }

    /**
     * Déclaration d'un script dans un contexte.
     *
     * @param string $tag Nom du contexte.
     * @param string $hook Nom du style.
     *
     * @return void
     */
    public function addScript($tag, $hook)
    {
        // Bypass
        if (!$this->routing()->exists($tag)) :
            return null;
        endif;

        if (!isset($this->scripts[$tag])) :
            $this->scripts[$tag] = [];
        endif;

        array_push($this->scripts[$tag], $hook);
    }

    /**
     * Vérification d'existence d'un script dans un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return bool
     */
    public function hasScript($tag)
    {
        return $this->routing()->is($tag) && !empty($this->scripts[$tag]);
    }

    /**
     * Récupération des scripts d'un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return array|false
     */
    public function getScripts($tag)
    {
        return $this->hasScript($tag) ? array_unique($this->scripts[$tag]) : false;
    }

    /**
     * SURCHARGE DES CONTEXTES WOOCOMMERCE EXISTANTS.
     */
    /**
     * Mise en file des pré-scripts globaux (exception - ne correspond pas à une conditionnel Woocommerce)
     */
    public function enqueue_scripts_before_global()
    {
    }

    /**
     * Mise en file des post-scripts globaux (exception - ne correspond pas à une conditionnel Woocommerce)
     */
    public function enqueue_scripts_after_global()
    {
    }

    /**
     * Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before($tag)
    {
    }

    /**
     * is_woocommerce() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_woocommerce()
    {
    }

    /**
     * is_shop() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_shop()
    {
    }

    /**
     * is_product() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_product()
    {
    }

    /**
     * is_product_category() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_product_category()
    {
    }

    /**
     * is_product_tag() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_product_tag()
    {
    }

    /**
     * is_product_cart() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_cart()
    {
    }

    /**
     * is_product_checkout() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_checkout()
    {
    }

    /**
     * is_account_page() - Mise en file des scripts avant les déclarations
     */
    public function enqueue_scripts_before_account_page()
    {
    }

    /**
     * Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after($tag)
    {
    }

    /**
     * is_woocommerce() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_woocommerce()
    {
    }

    /**
     * is_shop() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_shop()
    {
    }

    /**
     * is_product() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_product()
    {
    }

    /**
     * is_product_category() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_product_category()
    {
    }

    /**
     * is_product_tag() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_product_tag()
    {
    }

    /**
     * is_product_cart() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_cart()
    {
    }

    /**
     * is_product_checkout() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_checkout()
    {
    }

    /**
     * is_account_page() - Mise en file des scripts après les déclarations
     */
    public function enqueue_scripts_after_account_page()
    {
    }
}