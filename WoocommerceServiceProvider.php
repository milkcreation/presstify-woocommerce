<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use tiFy\Container\ServiceProvider;
use tiFy\Metabox\MetaboxManager;
use tiFy\Plugins\Woocommerce\Contracts\{
    Cart as CartContract,
    Checkout as CheckoutContract,
    Form as FormContract,
    Multistore as MultistoreContract,
    Order as OrderContract,
    PaymentGateways as PaymentGatewaysContract,
    ProductCat as ProductCatContract,
    Query as QueryContract,
    StoreFactory as StoreFactoryContract,
    ScriptLoader as ScriptLoaderContract,
    Shipping as ShippingContract,
    TemplateHooks as TemplateHooksContract,
    Woocommerce as WoocommerceContract};
use tiFy\Plugins\Woocommerce\{
    Cart\Cart,
    Checkout\Checkout,
    Form\Form,
    Functions\Functions,
    Mail\Mail,
    Metabox\Product as MetaboxProduct,
    Multistore\Multistore,
    Multistore\StoreFactory,
    Order\Order,
    PaymentGateway\PaymentGatewayBacs,
    PaymentGateway\PaymentGatewayCheque,
    PaymentGateway\PaymentGatewayCod,
    PaymentGateway\PaymentGatewayCustom,
    PaymentGateway\PaymentGatewayPaypal,
    PaymentGateway\PaymentGateways,
    Product\Product,
    ProductCat\ProductCat,
    Query\Query,
    Query\QueryProduct,
    Query\QueryProducts,
    Routing\Routing,
    ScriptLoader\ScriptLoader,
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
        'cart'                   => Cart::class,
        'checkout'               => Checkout::class,
        'form'                   => Form::class,
        'functions'              => Functions::class,
        'mail'                   => Mail::class,
        'metabox.product'        => MetaboxProduct::class,
        'multistore'             => Multistore::class,
        'multistore.factory'     => StoreFactory::class,
        'order'                  => Order::class,
        'payment-gateway.bacs'   => PaymentGatewayBacs::class,
        'payment-gateway.cod'    => PaymentGatewayCod::class,
        'payment-gateway.cheque' => PaymentGatewayCheque::class,
        'payment-gateway.custom' => PaymentGatewayCustom::class,
        'payment-gateway.paypal' => PaymentGatewayPaypal::class,
        'payment-gateways'       => PaymentGateways::class,
        'product'                => Product::class,
        'product-cat'            => ProductCat::class,
        'query'                  => Query::class,
        'query.product'          => QueryProduct::class,
        'query.products'         => QueryProducts::class,
        'routing'                => Routing::class,
        'script-loader'          => ScriptLoader::class,
        'shipping'               => Shipping::class,
        'shortcodes'             => Shortcodes::class,
        'views.template'         => Template::class,
        'views.template-hooks'   => TemplateHooks::class,
        'views.template_loader'  => TemplateLoader::class,
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
        'woocommerce.cart',
        'woocommerce.checkout',
        'woocommerce.form',
        'woocommerce.functions',
        'woocommerce.mail',
        'woocommerce.metabox.product',
        'woocommerce.multistore',
        'woocommerce.multistore.factory',
        'woocommerce.payment-gateway.bacs',
        'woocommerce.payment-gateway.cheque',
        'woocommerce.payment-gateway.cod',
        'woocommerce.payment-gateway.custom',
        'woocommerce.payment-gateway.paypal',
        'woocommerce.payment-gateways',
        'woocommerce.product',
        'woocommerce.product-cat',
        'woocommerce.order',
        'woocommerce.query',
        'woocommerce.query.product',
        'woocommerce.query.products',
        'woocommerce.routing',
        'woocommerce.script-loader',
        'woocommerce.shipping',
        'woocommerce.shortcodes',
        'woocommerce.views.template',
        'woocommerce.views.template-hooks',
        'woocommerce.views.template_loader',
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

            $this->getContainer()->get('woocommerce.cart');
            $this->getContainer()->get('woocommerce.checkout');
            $this->getContainer()->get('woocommerce.form');
            $this->getContainer()->get('woocommerce.mail');
            $this->getContainer()->get('woocommerce.metabox.product');
            $this->getContainer()->get('woocommerce.multistore');
            $this->getContainer()->get('woocommerce.order');
            $this->getContainer()->get('woocommerce.payment-gateways');
            $this->getContainer()->get('woocommerce.product');
            $this->getContainer()->get('woocommerce.product-cat');
            $this->getContainer()->get('woocommerce.query');
            $this->getContainer()->get('woocommerce.script-loader');
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
     * @return string|object
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
        $this->getContainer()->share('woocommerce', function () {
            return new Woocommerce($this->getContainer());
        });

        $this->registerCart();
        $this->registerCheckout();
        $this->registerForm();
        $this->registerFunctions();
        $this->registerMail();
        $this->registerMetaboxProduct();
        $this->registerMultistore();
        $this->registerOrder();
        $this->registerPaymentGateway();
        $this->registerProduct();
        $this->registerProductCat();
        $this->registerQuery();
        $this->registerRouting();
        $this->registerScriptLoader();
        $this->registerShipping();
        $this->registerShortcodes();
        $this->registerViewer();
        $this->registerViewsTemplate();
        $this->registerViewsTemplate();
        $this->registerViewsTemplateHooks();
        $this->registerViewsTemplateLoader();
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
            $instance = $concrete instanceof CartContract ? $concrete : new $concrete();

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
            $concrete = $this->getConcrete('checkout');

            /** @var CheckoutContract $instance */
            $instance = $concrete instanceof CheckoutContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.checkout', []))->parse();
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
            $concrete = $this->getConcrete('form');

            /** @var FormContract $instance */
            $instance = $concrete instanceof FormContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.form', []))->parse();
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
            $concrete = $this->getConcrete('multistore');

            /** @var MultistoreContract $instance */
            $instance = $concrete instanceof MultistoreContract ? $concrete : new $concrete();

            add_action('init', function () use ($instance) {
                if ($instance->all()) {
                    /** @var MetaboxManager $metabox */
                    $metabox = $this->manager->getContainer()->get('metabox');

                    $metabox->add('WoocommerceMultistore-storeOptions', 'tify_options@options', [
                        'title' => __('Multi-boutique woocommerce', 'theme'),
                    ]);
                }
            });

            add_action('woocommerce_get_shop_page_id', function ($page_id) use ($instance) {
                if (is_singular() && (in_array(get_the_ID(), $instance->getPageIds()))) {
                    $page_id = get_the_ID();
                }

                return $page_id;
            });

            return $instance->setManager($this->manager)->set(config('woocommerce.multistore', []));
        });

        $this->getContainer()->add('woocommerce.multistore.factory', function () {
            $concrete = $this->getConcrete('multistore.factory');

            /** @var StoreFactoryContract $instance */
            return $instance = $concrete instanceof StoreFactoryContract ? $concrete : new $concrete();
        });
    }

    /**
     * Déclaration du service de gestion des produits.
     *
     * @return void
     */
    public function registerPaymentGateway(): void
    {
        $this->getContainer()->share('woocommerce.payment-gateways', function () {
            $concrete = $this->getConcrete('payment-gateways');

            /** @var PaymentGatewaysContract $instance */
            $instance = new $concrete(config('woocommerce.payment-gateways', []));

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.bacs', function () {
            $concrete = $this->getConcrete('payment-gateway.bacs');

            /** @var PaymentGatewayBacs $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.cheque', function () {
            $concrete = $this->getConcrete('payment-gateway.cheque');

            /** @var PaymentGatewayCheque $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.cod', function () {
            $concrete = $this->getConcrete('payment-gateway.cod');

            /** @var PaymentGatewayCod $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.custom', function () {
            $concrete = $this->getConcrete('payment-gateway.custom');

            /** @var PaymentGatewayCustom $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.paypal', function () {
            $concrete = $this->getConcrete('payment-gateway.paypal');

            /** @var PaymentGatewayPaypal $instance */
            $instance = new $concrete();

            return $instance->setManager($this->manager);
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
            $instance = $concrete instanceof ProductCatContract ? $concrete : new $concrete();

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

            /** @var OrderContract $instance */
            $instance = $concrete instanceof OrderContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
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

            /** @var QueryContract $instance */
            $instance = $concrete instanceof QueryContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
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
     * Déclaration du service de chargement des scripts JS et Feuilles de styles CSS.
     *
     * @return void
     */
    public function registerScriptLoader(): void
    {
        $this->getContainer()->share('woocommerce.script-loader', function () {
            $concrete = $this->getConcrete('script-loader');

            /** @var ScriptLoaderContract $instance */
            $instance = $concrete instanceof ScriptLoaderContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
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

            /** @var ShippingContract $instance */
            $instance = $concrete instanceof ShippingContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
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