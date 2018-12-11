<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Plugins\Woocommerce\Assets\Assets;
use tiFy\Plugins\Woocommerce\Cart\Cart;
use tiFy\Plugins\Woocommerce\Checkout\Checkout;
use tiFy\Plugins\Woocommerce\Form\Form;
use tiFy\Plugins\Woocommerce\Functions\Functions;
use tiFy\Plugins\Woocommerce\Mail\Mail;
use tiFy\Plugins\Woocommerce\Metabox\Product;
use tiFy\Plugins\Woocommerce\Multishop\Multishop;
use tiFy\Plugins\Woocommerce\Multishop\Factory;
use tiFy\Plugins\Woocommerce\Order\Order;
use tiFy\Plugins\Woocommerce\Query\Query;
use tiFy\Plugins\Woocommerce\Routing\Routing;
use tiFy\Plugins\Woocommerce\Shipping\Shipping;
use tiFy\Plugins\Woocommerce\Shortcodes\Shortcodes;
use tiFy\Plugins\Woocommerce\Views\Template;
use tiFy\Plugins\Woocommerce\Views\TemplateHooks;
use tiFy\Plugins\Woocommerce\Views\TemplateLoader;

class WoocommerceServiceProvider extends AppServiceProvider
{
    protected $concrete = [
        'assets'                => Assets::class,
        'cart'                  => Cart::class,
        'checkout'              => Checkout::class,
        'form'                  => Form::class,
        'functions'             => Functions::class,
        'mail'                  => Mail::class,
        'metabox.product'       => Product::class,
        'multishop'             => Multishop::class,
        'multishop.factory'     => Factory::class,
        'order'                 => Order::class,
        'query'                 => Query::class,
        'routing'               => Routing::class,
        'shipping'              => Shipping::class,
        'shortcodes'            => Shortcodes::class,
        'views.template'        => Template::class,
        'views.template_hooks'  => TemplateHooks::class,
        'views.template_loader' => TemplateLoader::class
    ];

    /**
     * Liste des fournisseurs de service.
     * @var array
     */
    protected $provides = [
        'woocommerce',
        'woocommerce.assets',
        'woocommerce.cart',
        'woocommerce.checkout',
        'woocommerce.form',
        'woocommerce.functions',
        'woocommerce.mail',
        'woocommerce.metabox.product',
        'woocommerce.multishop',
        'woocommerce.multishop.factory',
        'woocommerce.order',
        'woocommerce.query',
        'woocommerce.routing',
        'woocommerce.shipping',
        'woocommerce.shortcodes',
        'woocommerce.views.template',
        'woocommerce.views.template_hooks',
        'woocommerce.views.template_loader'
    ];

    /**
     * Liste des services personnalisés.
     * @var array
     */
    protected $customs = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            'woocommerce.viewer',
            function () {
                $cinfo = class_info($this);
                $default_dir = $cinfo->getDirname() . '/Resources/views';
                $viewer = view()
                    ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                    ->setOverrideDir((($override_dir = config('woocommerce.viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname()));

                return $viewer;
            }
        );

        add_action('after_setup_tify', function () {
            $providers = config('woocommerce.providers', []);
            array_walk($providers, function ($v, $k) {
                $this->customs[$k] = $v;
            });
            $this->app->get('woocommerce');
            $this->app->get('woocommerce.assets');
            $this->app->get('woocommerce.cart');
            $this->app->get('woocommerce.checkout');
            $this->app->get('woocommerce.form');
            $this->app->get('woocommerce.mail');
            $this->app->get('woocommerce.metabox.product');
            $this->app->get('woocommerce.multishop');
            $this->app->get('woocommerce.order');
            $this->app->get('woocommerce.query');
            $this->app->get('woocommerce.shipping');
            $this->app->get('woocommerce.shortcodes');
            $this->app->get('woocommerce.views.template');
            $this->app->get('woocommerce.views.template_hooks');
            $this->app->get('woocommerce.views.template_loader');
        });
    }

    /**
     * Récupération de la classe de rappel d'un service.
     *
     * @param string $alias Alias de qualification.
     *
     * @return string
     */
    public function getConcrete($alias)
    {
        return $this->customs[$alias] ?? $this->concrete[$alias];
    }

    /**
     * Déclaration des services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->share('woocommerce', new Woocommerce());
        $this->registerAssets();
        $this->registerCart();
        $this->registerCheckout();
        $this->registerForm();
        $this->registerFunctions();
        $this->registerMail();
        $this->registerMetaboxProduct();
        $this->registerMultishop();
        $this->registerMultishopFactory();
        $this->registerOrder();
        $this->registerQuery();
        $this->registerRouting();
        $this->registerShipping();
        $this->registerShortcodes();
        $this->registerViewsTemplate();
        $this->registerViewsTemplateHooks();
        $this->registerViewsTemplateLoader();
    }

    /**
     * Déclaration du service de chargement des ressources.
     *
     * @return void
     */
    public function registerAssets()
    {
        $this->app->share('woocommerce.assets', function () {
            $attrs = config('woocommerce.assets', []);
            $concrete = $this->getConcrete('assets');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de gestion du panier.
     *
     * @return void
     */
    public function registerCart()
    {
        $this->app->share('woocommerce.cart', function () {
            $concrete = $this->getConcrete('cart');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion du processus de commande.
     *
     * @return void
     */
    public function registerCheckout()
    {
        $this->app->share('woocommerce.checkout', function () {
            $attrs = config('woocommerce.checkout', []);
            $concrete = $this->getConcrete('checkout');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de gestion des formulaires.
     *
     * @return void
     */
    public function registerForm()
    {
        $this->app->share('woocommerce.form', function () {
            $attrs = config('woocommerce.form', []);
            $concrete = $this->getConcrete('form');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service des fonctions utiles.
     *
     * @return void
     */
    public function registerFunctions()
    {
        $this->app->share('woocommerce.functions', new Functions());
    }

    /**
     * Déclaration du service de debug mail.
     *
     * @return void
     */
    public function registerMail()
    {
        $this->app->share('woocommerce.mail', function () {
            $concrete = $this->getConcrete('mail');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion backoffice des métadonnées produit.
     *
     * @return void
     */
    public function registerMetaboxProduct()
    {
        $this->app->share('woocommerce.metabox.product', function () {
            $concrete = $this->getConcrete('metabox.product');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion d'une multiboutique.
     *
     * @return void
     */
    public function registerMultishop()
    {
        $this->app->share('woocommerce.multishop', function () {
            $attrs = config('woocommerce.multishop', []);
            $concrete = $this->getConcrete('multishop');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de gestion d'une boutique dans l'environnement multiboutique.
     *
     * @return void
     */
    public function registerMultishopFactory()
    {
        $this->app->bind('woocommerce.multishop.factory', function ($shopId, $shopAttrs) {
            $concrete = $this->getConcrete('multishop.factory');

            return new $concrete($shopId, $shopAttrs);
        });
    }

    /**
     * Déclaration du service de gestion des commandes.
     *
     * @return void
     */
    public function registerOrder()
    {
        $this->app->share('woocommerce.order', function () {
            $concrete = $this->getConcrete('order');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des requêtes de récupération des produits.
     *
     * @return void
     */
    public function registerQuery()
    {
        $this->app->share('woocommerce.query', function () {
            $concrete = $this->getConcrete('query');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des routes.
     *
     * @return void
     */
    public function registerRouting()
    {
        $this->app->share('woocommerce.routing', function () {
            $attrs = config('woocommerce.routing', []);
            $concrete = $this->getConcrete('routing');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de gestion de l'expédition.
     *
     * @return void
     */
    public function registerShipping()
    {
        $this->app->share('woocommerce.shipping', function () {
            $concrete = $this->getConcrete('shipping');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des shortcodes.
     *
     * @return void
     */
    public function registerShortcodes()
    {
        $this->app->share('woocommerce.shortcodes', function () {
            $attrs = config('woocommerce.shortcodes', []);
            $concrete = $this->getConcrete('shortcodes');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de surcharge des éléments de template WooCommerce.
     *
     * @return void
     */
    public function registerViewsTemplate()
    {
        $this->app->share('woocommerce.views.template', function () {
            $concrete = $this->getConcrete('views.template');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des hooks de template de WooCommerce.
     *
     * @return void
     */
    public function registerViewsTemplateHooks()
    {
        $this->app->share('woocommerce.views.template_hooks', function () {
            $attrs = config('woocommerce.template_hooks', []);
            $concrete = $this->getConcrete('views.template_hooks');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de gestion du chargement des templates WooCommerce.
     *
     * @return void
     */
    public function registerViewsTemplateLoader()
    {
        $this->app->share('woocommerce.views.template_loader', function () {
            $concrete = $this->getConcrete('views.template_loader');

            return new $concrete(app(), config('woocommerce.template_loader', []));
        });
    }
}