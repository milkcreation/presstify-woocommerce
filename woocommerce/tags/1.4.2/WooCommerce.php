<?php

/**
 * @name WooCommerce
 * @desc Support de Woocommerce
 * @author Jordy Manner <jordy@milkcreation.fr> && Julien Picard <julien@tigreblanc.fr>
 * @package presstify-plugins/woocommerce
 * @namespace \tiFy\Plugins\WooCommerce
 * @version 1.4.2
 */

/**
 * @see https://docs.woocommerce.com/wc-apidocs/index.html
 */

namespace tiFy\Plugins\WooCommerce;

use tiFy\App\Plugin;
use tiFy\Plugins\WooCommerce\Admin\Product\MetaBoxes\MetaBoxes;
use tiFy\Plugins\WooCommerce\Cart;
use tiFy\Plugins\WooCommerce\CheckOut;
use tiFy\Plugins\WooCommerce\ConditionalTags;
use tiFy\Plugins\WooCommerce\Emails;
use tiFy\Plugins\WooCommerce\Forms;
use tiFy\Plugins\WooCommerce\MultiShop\MultiShop;
use tiFy\Plugins\WooCommerce\Order;
use tiFy\Plugins\WooCommerce\PaymentGateway\PaymentGateway;
use tiFy\Plugins\WooCommerce\Query;
use tiFy\Plugins\WooCommerce\ScriptLoader;
use tiFy\Plugins\WooCommerce\Shipping;
use tiFy\Plugins\WooCommerce\Shortcodes;
use tiFy\Plugins\WooCommerce\Template;
use tiFy\Plugins\WooCommerce\TemplateHooks;
use tiFy\Plugins\WooCommerce\TemplateLoader;
use tiFy\tiFy;

class WooCommerce extends Plugin
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Interface d'administration
        $this->appServiceAdd(MetaBoxes::class, self::loadOverride(MetaBoxes::class));

        // Panier
        $this->appServiceAdd(Cart::class, self::loadOverride(Cart::class));

        // Commande
        $Checkout = self::getOverride(Checkout::class);
        $this->appServiceAdd(Checkout::class, new $Checkout($this->appConfig('checkout')));

        // Chargement des controleurs
        // Identifiants de contexte
        $this->appServiceAdd(ConditionalTags::class, new ConditionalTags());

        // Tests et modification des emails
        $Emails = self::getOverride(Emails::class);
        $this->appServiceAdd(Emails::class, new $Emails($this->appConfig('emails')));

        // Modification des formulaires
        $Forms = self::getOverride(Forms::class);
        $this->appServiceAdd(Forms::class, new $Forms($this->appConfig('forms')));

        // Boutique multiple
        if ($multishop_conf = $this->appConfig('multishop')) :
            $MultiShop = self::getOverride(MultiShop::class);
            $this->appServiceAdd(MultiShop::class, new $MultiShop($multishop_conf));
        endif;

        // Paiement
        $this->appServiceAdd(Order::class, self::loadOverride(Order::class));

        // Requête de récupération des éléments de la boutique
        $this->appServiceAdd(Query::class, self::loadOverride(Query::class));

        // Chargement des scripts
        // Commande
        $ScriptLoader = self::getOverride(ScriptLoader::class);
        $this->appServiceAdd(ScriptLoader::class, new $ScriptLoader($this->appConfig('script_loader')));

        // Livraison
        $Shipping = self::getOverride(Shipping::class);
        $this->appServiceAdd(Shipping::class, new $Shipping(self::tFyAppConfig('shipping')));

        // Gestionnaire de shortcodes
        $Shortcodes = self::getOverride(Shortcodes::class);
        $this->appServiceAdd(Shortcodes::class, new $Shortcodes($this->appConfig('shortcodes')));

        // Eléments de templates
        $this->appServiceAdd(Template::class, self::loadOverride(Template::class));

        // Surchage des fonctions de template Woocommerce
        include $this->appDirname() . '/TemplateFunctions.php';
        self::getOverrideAppFile('TemplateFunctions.php');

        // Accrochage / Décrochage / Ordonnacement des éléments de template
        $TemplateHooks = self::getOverride(TemplateHooks::class);
        $this->appServiceAdd(TemplateHooks::class, new $TemplateHooks($this->appConfig('template-hooks')));

        // Plateformes de paiement    
        if ($payment_gateway_conf = $this->appConfig('payment_gateway')) :
            $PaymentGateway = self::getOverride(PaymentGateway::class);
            $this->appServiceAdd(PaymentGateway::class ,new $PaymentGateway($payment_gateway_conf));
        endif;

        // Chargement des templates avec le moteur de gabarit PHP Plates
        $appClassname = tiFy::getConfig('app.namespace') . "\\" . tiFy::getConfig('app.bootstrap');
        $service = $this->appConfig('template_loader') ?: (class_exists($appClassname) ? $appClassname : '');

        if ($app = $this->appServiceGet($service)) :
            new TemplateLoader($app);
        endif;

        // Fonctions d'aide à la saisie
        include $this->appDirname() . '/Helpers.php';

        // Déclenchement des événements
        $this->appAddAction('init', null, 1);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        add_theme_support('woocommerce');
    }
}
