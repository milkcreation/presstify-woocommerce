<?php

/**
 * @name Woocommerce
 * @desc Support de Woocommerce
 * @author Jordy Manner <jordy@milkcreation.fr> && Julien Picard <julien@tigreblanc.fr>
 * @package presstify-plugins/woocommerce
 * @namespace \tiFy\Plugins\Woocommerce
 * @version 2.0.0
 */

/**
 * @see https://docs.woocommerce.com/wc-apidocs/index.html
 */

namespace tiFy\Plugins\Woocommerce;

use tiFy\Plugins\Woocommerce\Contracts\Woocommerce as WoocommerceContract;

/**
 * Class Woocommerce
 * @package tiFy\Plugins\Woocommerce
 *
 * Activation :
 * ----------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Woocommerce\WoocommerceServiceProvider à la liste des fournisseurs de
 * services chargés automatiquement par l'application.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\Woocommerce\WoocommerceServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          WoocommerceServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration :
 * ----------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier woocommerce.php
 * @see /vendor/presstify-plugins/woocommerce/Resources/config/woocommerce.php Exemple de configuration
 */
class Woocommerce implements WoocommerceContract
{
    use WoocommerceResolverTrait;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        return;

        // Interface d'administration
        $this->appServiceAdd(MetaBoxes::class, self::loadOverride(MetaBoxes::class));

        // Panier
        $this->appServiceAdd(Cart::class, self::loadOverride(Cart::class));

        // Commande
        $Checkout = self::getOverride(Checkout::class);
        $this->appServiceAdd(Checkout::class, new $Checkout($this->appConfig('checkout')));

        // Chargement des controleurs
        // Identifiants de contexte
        $this->appServiceAdd(Routing::class, new Routing());

        // Tests et modification des emails
        $Emails = self::getOverride(Mail::class);
        $this->appServiceAdd(Mail::class, new $Emails($this->appConfig('emails')));

        // Modification des formulaires
        $Forms = self::getOverride(Form::class);
        $this->appServiceAdd(Form::class, new $Forms($this->appConfig('forms')));

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
        $ScriptLoader = self::getOverride(Assets::class);
        $this->appServiceAdd(Assets::class, new $ScriptLoader($this->appConfig('script_loader')));

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
            new TemplateLoader($app, $this->appConfig('views'));
        endif;

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
