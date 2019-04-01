<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Plugins\Woocommerce\Assets\Assets;
use tiFy\Plugins\Woocommerce\Cart\Cart;
use tiFy\Plugins\Woocommerce\Checkout\Checkout;
use tiFy\Plugins\Woocommerce\Form\Form;
use tiFy\Plugins\Woocommerce\Functions\Functions;
use tiFy\Plugins\Woocommerce\Mail\Mail;
use tiFy\Plugins\Woocommerce\Metabox\Product as MetaboxProduct;
use tiFy\Plugins\Woocommerce\Multishop\Multishop;
use tiFy\Plugins\Woocommerce\Multishop\Factory;
use tiFy\Plugins\Woocommerce\Order\Order;
use tiFy\Plugins\Woocommerce\Product\Product;
use tiFy\Plugins\Woocommerce\Query\Query;
use tiFy\Plugins\Woocommerce\Query\QueryProduct;
use tiFy\Plugins\Woocommerce\Query\QueryProducts;
use tiFy\Plugins\Woocommerce\Routing\Routing;
use tiFy\Plugins\Woocommerce\Shipping\Shipping;
use tiFy\Plugins\Woocommerce\Shortcodes\Shortcodes;
use tiFy\Plugins\Woocommerce\Views\Template;
use tiFy\Plugins\Woocommerce\Views\TemplateHooks;
use tiFy\Plugins\Woocommerce\Views\TemplateLoader;
use WC_Product;
use WP_Query;

class WoocommerceServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services par défaut.
     * @var array
     */
    protected $concrete = [
        'assets'                => Assets::class,
        'cart'                  => Cart::class,
        'checkout'              => Checkout::class,
        'form'                  => Form::class,
        'functions'             => Functions::class,
        'mail'                  => Mail::class,
        'metabox.product'       => MetaboxProduct::class,
        'multishop'             => Multishop::class,
        'multishop.factory'     => Factory::class,
        'order'                 => Order::class,
        'product'               => Product::class,
        'query'                 => Query::class,
        'query.product'         => QueryProduct::class,
        'query.products'        => QueryProducts::class,
        'routing'               => Routing::class,
        'shipping'              => Shipping::class,
        'shortcodes'            => Shortcodes::class,
        'views.template'        => Template::class,
        'views.template_hooks'  => TemplateHooks::class,
        'views.template_loader' => TemplateLoader::class
    ];

    /**
     * Liste des services personnalisés.
     * @var array
     */
    protected $customs = [];

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
        'woocommerce.product',
        'woocommerce.order',
        'woocommerce.query',
        'woocommerce.query.product',
        'woocommerce.query.products',
        'woocommerce.routing',
        'woocommerce.shipping',
        'woocommerce.shortcodes',
        'woocommerce.views.template',
        'woocommerce.views.template_hooks',
        'woocommerce.views.template_loader'
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('after_setup_theme', function () {
            $this->getContainer()->get('woocommerce');

            $providers = config('woocommerce.providers', []);
            array_walk($providers, function ($v, $k) {
                $this->customs[$k] = $v;
            });
            $this->getContainer()->get('woocommerce.assets');
            $this->getContainer()->get('woocommerce.cart');
            $this->getContainer()->get('woocommerce.checkout');
            $this->getContainer()->get('woocommerce.form');
            $this->getContainer()->get('woocommerce.mail');
            $this->getContainer()->get('woocommerce.metabox.product');
            $this->getContainer()->get('woocommerce.multishop');
            $this->getContainer()->get('woocommerce.order');
            $this->getContainer()->get('woocommerce.product');
            $this->getContainer()->get('woocommerce.query');
            $this->getContainer()->get('woocommerce.shipping');
            $this->getContainer()->get('woocommerce.shortcodes');
            $this->getContainer()->get('woocommerce.views.template');
            $this->getContainer()->get('woocommerce.views.template_hooks');
            $this->getContainer()->get('woocommerce.views.template_loader');
        });

        add_action('init', function () {
            add_theme_support('woocommerce');
        },1);
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
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('woocommerce', function() {
            return new Woocommerce();
        });

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
        $this->registerProduct();
        $this->registerQuery();
        $this->registerRouting();
        $this->registerShipping();
        $this->registerShortcodes();
        $this->registerViewer();
        $this->registerViewsTemplate();
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
        $this->getContainer()->share('woocommerce.assets', function () {
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
        $this->getContainer()->share('woocommerce.cart', function () {
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
        $this->getContainer()->share('woocommerce.checkout', function () {
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
        $this->getContainer()->share('woocommerce.form', function () {
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
        $this->getContainer()->share('woocommerce.functions', function () {
             return new Functions();
        });
    }

    /**
     * Déclaration du service de debug mail.
     *
     * @return void
     */
    public function registerMail()
    {
        $this->getContainer()->share('woocommerce.mail', function () {
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
        $this->getContainer()->share('woocommerce.metabox.product', function () {
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
        $this->getContainer()->share('woocommerce.multishop', function () {
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
        $this->getContainer()->add('woocommerce.multishop.factory', function ($shopId, $shopAttrs) {
            $concrete = $this->getConcrete('multishop.factory');

            return new $concrete($shopId, $shopAttrs);
        });
    }

    /**
     * Déclaration du service de gestion des produits.
     *
     * @return void
     */
    public function registerProduct()
    {
        $this->getContainer()->share('woocommerce.product', function () {
            $concrete = $this->getConcrete('product');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des commandes.
     *
     * @return void
     */
    public function registerOrder()
    {
        $this->getContainer()->share('woocommerce.order', function () {
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
        $this->getContainer()->share('woocommerce.query', function () {
            $concrete = $this->getConcrete('query');

            return new $concrete();
        });

        $this->getContainer()->add('woocommerce.query.product', function ($wc_product = null) {
            /** @var QueryProduct $concrete */
            $concrete = $this->getConcrete('query.product');

            if ($wc_product instanceof WC_Product) {
                return new $concrete($wc_product);
            } elseif (is_numeric($wc_product)) {
                return $concrete::createFromId($wc_product);
            } else {
                return $concrete::createFromGlobal();
            }
        });

        $this->getContainer()->add('woocommerce.query.products', function ($wp_query = null) {
            /** @var QueryProducts $concrete */
            $concrete = $this->getConcrete('query.products');

            return $wp_query instanceof WP_Query ? new $concrete($wp_query) : $concrete::createFromGlobals();
        });
    }

    /**
     * Déclaration du service de gestion des routes.
     *
     * @return void
     */
    public function registerRouting()
    {
        $this->getContainer()->share('woocommerce.routing', function () {
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
        $this->getContainer()->share('woocommerce.shipping', function () {
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
        $this->getContainer()->share('woocommerce.shortcodes', function () {
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
    public function registerViewer()
    {
        $this->getContainer()->share('woocommerce.viewer', function () {
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
    }

    /**
     * Déclaration du service de surcharge des éléments de template WooCommerce.
     *
     * @return void
     */
    public function registerViewsTemplate()
    {
        $this->getContainer()->share('woocommerce.views.template', function () {
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
        $this->getContainer()->share('woocommerce.views.template_hooks', function () {
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
        $this->getContainer()->share('woocommerce.views.template_loader', function () {
            $concrete = $this->getConcrete('views.template_loader');

            return new $concrete(app(), config('woocommerce.template_loader', []));
        });
    }
}