<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\ScriptLoader;

use tiFy\Plugins\Woocommerce\{Contracts\ScriptLoader as ScriptLoaderContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

class ScriptLoader extends ParamsBag implements ScriptLoaderContract
{
    use WoocommerceAwareTrait;

    /**
     * Préfixe des scripts minifiés.
     * @var string
     */
    protected $min = '';

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
     * Liste des scripts Woocommerce.
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
        'woocommerce'                => true,
    ];

    /**
     * Liste des feuilles de styles Woocommerce.
     * @see wp-content/plugins/woocommerce/includes/class-wc-frontend-scripts.php
     * @var array
     */
    protected $wcStyles = [
        'woocommerce-layout'      => true,
        // ! woocommerce-smallscreen est une dépendance de woocommerce-layout
        'woocommerce-smallscreen' => true,
        'woocommerce-general'     => true,
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct(array $attrs = [])
    {
        $this->set($attrs)->parse();

        $this->wcStyles = array_merge($this->wcStyles, $this->get('wc_enqueue_styles', []));
        $this->wcScripts = array_merge($this->wcScripts, $this->get('wc_enqueue_scripts', []));
        $this->min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        // Déclaration des styles/scripts de l'application.
        add_action('init', function () {
            foreach ($this->manager->routing()->getRoutes() as $route) {
                if (file_exists(get_stylesheet_directory() . "/dist/css/wc-{$route}" . $this->min . '.css')) {
                    wp_register_style('tify_wc_' . $route,
                        get_stylesheet_directory_uri() . "/dist/css/wc-{$route}" . $this->min . '.css');
                    $this->addStyle($route, 'tify_wc_' . $route);
                }
                if (file_exists(get_stylesheet_directory() . "/dist/js/wc-{$route}" . $this->min . ".js")) {
                    wp_register_script('tify_wc_' . $route,
                        get_stylesheet_directory_uri() . "/dist/js/wc-{$route}" . $this->min . '.js');
                    $this->addScript($route, 'tify_wc_' . $route);
                }
            }
        });

        // Désactivation des styles natifs WooCommerce.
        add_filter('woocommerce_enqueue_styles', function (array $styles) {
            foreach ($this->wcStyles as $handle => $active) {
                if (!$active) {
                    unset($styles[$handle]);
                }
            }
            return $styles;
        });

        // Désactivation des scripts natifs WooCommerce.
        add_action('wp_enqueue_scripts', function () {
            foreach ($this->wcScripts as $handle => $active) {
                if (!$active) {
                    wp_dequeue_script($handle);
                }
            }
        }, 25);

        // Mise en file scripts.
        add_action('wp_enqueue_scripts', function () {
            if ($this->manager->form()->isSelectJsEnabled() &&
                ($this->manager->routing()->is('checkout') || $this->manager->routing()->is('account_page'))
            ) {
                wp_dequeue_script('selectWoo');
                wp_dequeue_script('select2');
                wp_dequeue_style('select2');
            }

            foreach ($this->manager->routing()->getRoutes() as $route) {
                if ($this->manager->routing()->is($route)) {
                    $this->enqueue_scripts_before($route);
                    if (is_callable([$this, 'enqueue_scripts_before_' . $route])) {
                        call_user_func([$this, 'enqueue_scripts_before_' . $route]);
                    }

                    if ($this->hasStyle($route)) {
                        foreach ($this->getStyles($route) as $style) {
                            wp_enqueue_style($style);
                        }
                    }

                    if ($this->hasScript($route)) {
                        foreach ($this->getScripts($route) as $script) {
                            wp_enqueue_script($script);
                        }
                    }

                    if (is_callable([$this, 'enqueue_scripts_after_' . $route])) {
                        call_user_func([$this, 'enqueue_scripts_after_' . $route]);
                    }
                    $this->enqueue_scripts_after($route);
                }
            }
        }, 999999);
    }

    /**
     * @inheritDoc
     */
    public function addScript(string $tag, string $hook): void
    {
        if (!$this->manager->routing()->exists($tag)) {
            return;
        } elseif (!isset($this->scripts[$tag])) {
            $this->scripts[$tag] = [];
        }

        array_push($this->scripts[$tag], $hook);
    }

    /**
     * @inheritDoc
     */
    public function addStyle(string $tag, string $hook): void
    {
        if (!$this->manager->routing()->exists($tag)) {
            return;
        } elseif (!isset($this->styles[$tag])) {
            $this->styles[$tag] = [];
        }

        array_push($this->styles[$tag], $hook);
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after(string $tag): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_account_page(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_cart(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_checkout(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_product(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_product_category(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_product_tag(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_shop(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_after_woocommerce(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before(string $tag): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_account_page(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_cart(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_checkout(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_product(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_product_category(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_product_tag(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_shop(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function enqueue_scripts_before_woocommerce(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function hasScript($tag): bool
    {
        return $this->manager->routing()->is($tag) && !empty($this->scripts[$tag]);
    }

    /**
     * @inheritDoc
     */
    public function hasStyle(string $tag): bool
    {
        return $this->manager->routing()->is($tag) && !empty($this->styles[$tag]);
    }

    /**
     * @inheritDoc
     */
    public function getScripts(string $tag): array
    {
        return $this->hasScript($tag) ? array_unique($this->scripts[$tag]) : [];
    }

    /**
     * @inheritDoc
     */
    public function getStyles(string $tag): array
    {
        return $this->hasStyle($tag) ? array_unique($this->styles[$tag]) : [];
    }
}