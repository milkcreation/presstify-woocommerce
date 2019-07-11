<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use tiFy\Container\ServiceProvider;
use tiFy\Metabox\MetaboxManager;
use tiFy\Plugins\Woocommerce\Contracts\{
    Cart as CartContract,
    Multistore as MultistoreContract,
    ProductCat as ProductCatContract,
    TemplateHooks as TemplateHooksContract,
    Woocommerce as WoocommerceContract};
use tiFy\Plugins\Woocommerce\{
    Assets\Assets,
    Cart\Cart,
    Checkout\Checkout,
    Form\Form,
    Functions\Functions,
    Mail\Mail,
    Metabox\Product as MetaboxProduct,
    Multistore\Multistore,
    Order\Order,
    Product\Product,
    ProductCat\ProductCat,
    Query\Query,
    Query\QueryProduct,
    Query\QueryProducts,
    Routing\Routing,
    Shipping\Shipping,
    Shortcodes\Shortcodes,
    Views\Template,
    Views\TemplateHooks,
    Views\TemplateLoader};
use WC_Product;
use WP_Query;

class WoocommerceServiceProvider extends ServiceProvider
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
        'multistore'            => Multistore::class,
        'order'                 => Order::class,
        'product'               => Product::class,
        'product-cat'           => ProductCat::class,
        'query'                 => Query::class,
        'query.product'         => QueryProduct::class,
        'query.products'        => QueryProducts::class,
        'routing'               => Routing::class,
        'shipping'              => Shipping::class,
        'shortcodes'            => Shortcodes::class,
        'views.template'        => Template::class,
        'views.template-hooks'  => TemplateHooks::class,
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
        'woocommerce.product-cat',
        'woocommerce.order',
        'woocommerce.query',
        'woocommerce.query.product',
        'woocommerce.query.products',
        'woocommerce.routing',
        'woocommerce.shipping',
        'woocommerce.shortcodes',
        'woocommerce.views.template',
        'woocommerce.views.template-hooks',
        'woocommerce.views.template_loader'
    ];

    /**
     * Instaance du gestionnaire de plugin.
     * @var WoocommerceContract
     */
    protected $manager;

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('after_setup_theme', function () {
            $this->manager = $this->getContainer()->get('woocommerce');

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
            $this->getContainer()->get('woocommerce.multistore');
            $this->getContainer()->get('woocommerce.order');
            $this->getContainer()->get('woocommerce.product');
            $this->getContainer()->get('woocommerce.product-cat');
            $this->getContainer()->get('woocommerce.query');
            $this->getContainer()->get('woocommerce.shipping');
            $this->getContainer()->get('woocommerce.shortcodes');
            $this->getContainer()->get('woocommerce.views.template');
            $this->getContainer()->get('woocommerce.views.template-hooks');
            $this->getContainer()->get('woocommerce.views.template_loader');
        });

        add_action('init', function () {
            add_theme_support('woocommerce');
        }, 1);
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
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('woocommerce', function() {
            return new Woocommerce($this->getContainer());
        });

        $this->registerAssets();
        $this->registerCart();
        $this->registerCheckout();
        $this->registerForm();
        $this->registerFunctions();
        $this->registerMail();
        $this->registerMetaboxProduct();
        $this->registerMultistore();
        $this->registerOrder();
        $this->registerProduct();
        $this->registerProductCat();
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
    public function registerAssets(): void
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
    public function registerCart(): void
    {
        $this->getContainer()->share('woocommerce.cart', function () {
            $concrete = $this->getConcrete('cart');

            /** @var CartContract $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
        });
    }

    /**
     * Déclaration du service de gestion du processus de commande.
     *
     * @return void
     */
    public function registerCheckout(): void
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
    public function registerForm(): void
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
    public function registerFunctions(): void
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
    public function registerMail(): void
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
    public function registerMetaboxProduct(): void
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
    public function registerMultistore(): void
    {
        $this->getContainer()->share('woocommerce.multistore', function () {
            $stores = config('woocommerce.multistore', []);
            $concrete = $this->getConcrete('multistore');

            /** @var MultistoreContract $instance */
            $instance = new $concrete($this->manager);

            add_action('init', function () use ($instance) {
                if ($instance->all()) {
                    /** @var MetaboxManager $metabox */
                    $metabox = $this->manager->getContainer()->get('metabox');

                    $metabox->add('WoocommerceMultistore-storeOptions', 'tify_options@options', [
                        'title' => __('Multi-boutique woocommerce', 'theme')
                    ]);
                }
            });

            add_action('woocommerce_get_shop_page_id', function ($page_id) use ($instance) {
                if (is_singular() && (in_array(get_the_ID(), $instance->getPageIds()))) {
                    $page_id = get_the_ID();
                }

                return $page_id;
            });

            return $instance->setManager($this->manager)->set($stores);
        });

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
    public function registerProduct(): void
    {
        $this->getContainer()->share('woocommerce.product', function () {
            $concrete = $this->getConcrete('product');

            return new $concrete();
        });
    }

    /**
     * Déclaration du gestionnaire de catégories de produits.
     *
     * @return void
     */
    public function registerProductCat(): void
    {
        $this->getContainer()->share('woocommerce.product-cat', function () {
            $concrete = $this->getConcrete('product-cat');

            /** @var ProductCatContract $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
        });
    }

    /**
     * Déclaration du service de gestion des commandes.
     *
     * @return void
     */
    public function registerOrder(): void
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
    public function registerQuery(): void
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
    public function registerRouting(): void
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
    public function registerShipping(): void
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
    public function registerShortcodes(): void
    {
        $this->getContainer()->share('woocommerce.shortcodes', function () {
            $attrs = config('woocommerce.shortcodes', []);
            $concrete = $this->getConcrete('shortcodes');

            return new $concrete($attrs);
        });
    }

    /**
     * Déclaration du service de surcharge des éléments de template Woocommerce.
     *
     * @return void
     */
    public function registerViewer(): void
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
     * Déclaration du service de surcharge des éléments de template Woocommerce.
     *
     * @return void
     */
    public function registerViewsTemplate(): void
    {
        $this->getContainer()->share('woocommerce.views.template', function () {
            $concrete = $this->getConcrete('views.template');

            return new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des hooks de template de Woocommerce.
     *
     * @return void
     */
    public function registerViewsTemplateHooks(): void
    {
        $this->getContainer()->share('woocommerce.views.template-hooks', function () {
            $concrete = $this->getConcrete('views.template-hooks');

            /** @var TemplateHooksContract $instance */
            $instance = new $concrete();

            return $instance->set(config('woocommerce.template-hooks', []))->parse();
        });
    }

    /**
     * Déclaration du service de gestion du chargement des templates Woocommerce.
     *
     * @return void
     */
    public function registerViewsTemplateLoader(): void
    {
        $this->getContainer()->share('woocommerce.views.template_loader', function () {
            $concrete = $this->getConcrete('views.template_loader');

            return new $concrete(app(), config('woocommerce.template_loader', []));
        });
    }
}