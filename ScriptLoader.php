<?php
/**
 * @Overrideable
 */

namespace tiFy\Plugins\WooCommerce;

use tiFy\Core\ScriptLoader\ScriptLoader as tiFyScriptLoader;
use \tiFy\Plugins\WooCommerce\ConditionalTags as Tags;
use \tiFy\Plugins\WooCommerce\Forms;

/**
 * CHARGEMENT DES SCRIPTS
 * - Désactivation des scripts natif de Woocommerce
 * - Chargement de script en contexte
 */
class ScriptLoader extends \tiFy\App\Factory
{
    /**
     * Liste des styles déclarés par contexte
     */
    protected static $Styles = [];

    /**
     * Liste des scripts déclarés par contexte
     */
    protected static $Scripts = [];

    /**
     * Préfixe des scripts minifiés
     */
    protected static $Min = '';

    /**
     * Chargement des Feuilles de styles natives Woocommerce
     *
     * @see wp-content/plugins/woocommerce/includes/class-wc-frontend-scripts.php
     */
    protected static $WcEnqueueStyles = [
        'woocommerce-layout'      => true,
        'woocommerce-smallscreen' => true,    // ! Dépendance woocommerce-layout
        'woocommerce-general'     => true
    ];

    /**
     * Chargement des Scripts JS natifs Woocommerce
     *
     * @see wp-content/plugins/woocommerce/includes/class-wc-frontend-scripts.php
     */
    protected static $WcEnqueueScripts = [
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
     * CONSTRUCTEUR
     */
    public function __construct($attrs = [])
    {
        parent::__construct();

        // Traitement des attributs
        /// Chargement des Feuilles de styles
        if (!empty($attrs['wc_enqueue_styles']))
            self::$WcEnqueueStyles = wp_parse_args($attrs['wc_enqueue_styles'], self::$WcEnqueueStyles);

        /// Chargement des Scripts JS
        if (!empty($attrs['wc_enqueue_scripts']))
            self::$WcEnqueueScripts = wp_parse_args($attrs['wc_enqueue_scripts'], self::$WcEnqueueScripts);

        add_action('init', [$this, 'init']);
        add_filter('woocommerce_enqueue_styles', [$this, 'woocommerce_enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'], 25);
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation globale
     */
    final public function init()
    {
        // STYLES        
        foreach (Tags::getAll() as $tag) :
            if (file_exists(get_stylesheet_directory() . "/dist/css/wc-{$tag}" . static::$Min . ".css")) :
                tiFyScriptLoader::register_style(
                    'tify_wc_' . $tag,
                    [
                        'src' => get_stylesheet_directory_uri() . "/dist/css/wc-{$tag}" . static::$Min . ".css"
                    ]
                );
                self::addStyle($tag, 'tify_wc_' . $tag);
            endif;
        endforeach;

        // SCRIPTS       
        foreach (Tags::getAll() as $tag) :
            if (file_exists(get_stylesheet_directory() . "/dist/js/wc-{$tag}" . static::$Min . ".js")) :
                tiFyScriptLoader::register_script(
                    'tify_wc_' . $tag,
                    [
                        'src' => get_stylesheet_directory_uri() . "/dist/js/wc-{$tag}" . static::$Min . ".js"
                    ]
                );
                self::addScript($tag, 'tify_wc_' . $tag);
            endif;
        endforeach;
    }

    /**
     * Chargement des feuilles de styles de l'interface utilisateur
     */
    final public function woocommerce_enqueue_styles($styles)
    {
        foreach (self::$WcEnqueueStyles as $handle => $bypass) :
            if ($bypass)
                continue;
            unset($styles[$handle]);
        endforeach;

        return $styles;
    }

    /**
     * Mise en file des scripts de l'interface utilisateur
     */
    final public function wp_enqueue_scripts()
    {
        /**
         * Désactivation des scripts natifs de woocommerce
         */
        foreach (self::$WcEnqueueScripts as $handle => $bypass) :
            if ($bypass)
                continue;
            wp_dequeue_script($handle);
        endforeach;

        /**
         * Remplacement de la liste de selection des pays par tiFyDropdown
         */
        if (Forms::istiFyDropdownCountry()) :
            if (is_checkout() || is_account_page()) :
                tify_control_enqueue('dropdown');
                wp_dequeue_script('select2');
                wp_dequeue_style('select2');
            endif;
        endif;

        // Script Global
        $this->enqueue_scripts_before_global();
        if (file_exists(get_stylesheet_directory() . "/dist/css/wc-global" . static::$Min . ".css")) :
            wp_enqueue_style('tify_wc_global', get_stylesheet_directory_uri() . "/dist/css/wc-global" . static::$Min . ".css");
        endif;
        if (file_exists(get_stylesheet_directory() . "/dist/js/wc-global" . static::$Min . ".js")) :
            wp_enqueue_script('tify_wc_global', get_stylesheet_directory_uri() . "/dist/js/wc-global" . static::$Min . ".js");
        endif;
        $this->enqueue_scripts_after_global();

        foreach (Tags::getAll() as $tag) :
            if (Tags::isCurrent($tag)) :
                $this->enqueue_scripts_before($tag);

                if (is_callable([$this, 'enqueue_scripts_before_' . $tag])) :
                    call_user_func([$this, 'enqueue_scripts_before_' . $tag]);
                endif;

                if (self::hasStyle($tag)) :
                    foreach (self::getStyles($tag) as $hook) :
                        wp_enqueue_style($hook);
                    endforeach;
                endif;
                if (self::hasScript($tag)) :
                    foreach (self::getScripts($tag) as $hook) :
                        wp_enqueue_script($hook);
                    endforeach;
                endif;

                $this->enqueue_scripts_after($tag);
                if (is_callable([$this, 'enqueue_scripts_after_' . $tag])) :
                    call_user_func([$this, 'enqueue_scripts_after_' . $tag]);
                endif;
            endif;
        endforeach;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Déclaration d'un style dans un contexte
     */
    final public static function addStyle($tag, $hook)
    {
        // Bypass
        if (!Tags::is($tag))
            return;

        if (!isset(self::$Styles[$tag]))
            self::$Styles[$tag] = [];

        array_push(self::$Styles[$tag], $hook);
    }

    /**
     * Vérification d'existance d'un style dans un contexte
     */
    final public static function hasStyle($tag)
    {
        // Bypass
        if (!Tags::is($tag))
            return false;

        return !empty(self::$Styles[$tag]);
    }

    /**
     * Récupération des styles d'un contexte
     */
    final public static function getStyles($tag)
    {
        // Bypass
        if (!self::hasStyle($tag))
            return [];

        return array_unique(self::$Styles[$tag]);
    }

    /**
     * Déclaration d'un script dans un contexte
     */
    final public static function addScript($tag, $hook)
    {
        // Bypass
        if (!Tags::is($tag))
            return;

        if (!isset(self::$Scripts[$tag]))
            self::$Scripts[$tag] = [];

        array_push(self::$Scripts[$tag], $hook);
    }

    /**
     * Vérification d'existance d'un script dans un contexte
     */
    final public static function hasScript($tag)
    {
        // Bypass
        if (!Tags::is($tag))
            return false;

        return !empty(self::$Scripts[$tag]);
    }

    /**
     * Récupération des scripts d'un contexte
     */
    final public static function getScripts($tag)
    {
        // Bypass
        if (!self::hasScript($tag))
            return [];

        return array_unique(self::$Scripts[$tag]);
    }

    /**
     * Définition de la minification des scripts
     */
    public function setMin()
    {
        return SCRIPT_DEBUG ? '' : '.min';
    }

    /**
     * SURCHARGE
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