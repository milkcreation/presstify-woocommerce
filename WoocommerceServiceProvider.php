<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\Woocommerce\{
    Cart\Cart,
    Checkout\Checkout,
    Form\Form,
    Functions\Functions,
    Mail\Mail,
    Metabox\MetaboxProduct,
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
    Query\QueryOrder,
    Routing\Routing,
    ScriptLoader\ScriptLoader,
    Shipping\Shipping,
    Shortcodes\Shortcodes,
    Store\StoreFactory,
    Store\Stores,
    TemplateFilters\TemplateFilters,
    TemplateHooks\TemplateHooks,
    TemplateLoader\TemplateLoader
};
use tiFy\Plugins\Woocommerce\Contracts\{Cart as CartContract,
    Checkout as CheckoutContract,
    Form as FormContract,
    MetaboxProduct as MetaboxProductContract,
    Order as OrderContract,
    PaymentGatewayBacs as PaymentGatewayBacsContract,
    PaymentGatewayCheque as PaymentGatewayChequeContract,
    PaymentGatewayCod as PaymentGatewayCodContract,
    PaymentGatewayCustom as PaymentGatewayCustomContract,
    PaymentGatewayPaypal as PaymentGatewayPaypalContract,
    PaymentGateways as PaymentGatewaysContract,
    Product as ProductContract,
    ProductCat as ProductCatContract,
    Query as QueryContract,
    QueryProduct as QueryProductContract,
    QueryOrder as QueryOrderContract,
    ScriptLoader as ScriptLoaderContract,
    Shipping as ShippingContract,
    Shortcodes as ShortcodesContract,
    StoreFactory as StoreFactoryContract,
    Stores as StoresContract,
    TemplateFilters as TemplateFiltersContract,
    TemplateHooks as TemplateHooksContract,
    TemplateLoader as TemplateLoaderContract,
    Woocommerce as WoocommerceContract
};
use tiFy\Support\Proxy\{Metabox, View};
use tiFy\Wordpress\Query\QueryPost;

class WoocommerceServiceProvider extends ServiceProvider
{
    /**
     * Liste des services par d??faut.
     * @var array
     */
    protected $concrete = [
        'cart'                   => Cart::class,
        'checkout'               => Checkout::class,
        'form'                   => Form::class,
        'functions'              => Functions::class,
        'mail'                   => Mail::class,
        'metabox-product'        => MetaboxProduct::class,
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
        'query.order'            => QueryOrder::class,
        'routing'                => Routing::class,
        'script-loader'          => ScriptLoader::class,
        'shipping'               => Shipping::class,
        'shortcodes'             => Shortcodes::class,
        'stores'                 => Stores::class,
        'store-factory'          => StoreFactory::class,
        'template-filters'       => TemplateFilters::class,
        'template-hooks'         => TemplateHooks::class,
        'views.template-loader'  => TemplateLoader::class,
    ];

    /**
     * Liste des services personnalis??s.
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
        'woocommerce.metabox-product',
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
        'woocommerce.query.order',
        'woocommerce.routing',
        'woocommerce.script-loader',
        'woocommerce.shipping',
        'woocommerce.shortcodes',
        'woocommerce.store-factory',
        'woocommerce.stores',
        'woocommerce.template-filters',
        'woocommerce.template-hooks',
        'woocommerce.views.template-loader',
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
        $this->manager = new Woocommerce($this->getContainer());

        $this->getContainer()->share('woocommerce', $this->manager);

        add_action('after_setup_theme', function () {
            $this->getContainer()->get('woocommerce');

            $providers = config('woocommerce.providers', []);
            array_walk($providers, function ($v, $k) {
                $this->customs[$k] = $v;
            });

            $this->getContainer()->get('woocommerce.cart');
            $this->getContainer()->get('woocommerce.checkout');
            $this->getContainer()->get('woocommerce.form');
            $this->getContainer()->get('woocommerce.functions');
            $this->getContainer()->get('woocommerce.mail');
            $this->getContainer()->get('woocommerce.metabox-product');
            $this->getContainer()->get('woocommerce.order');
            $this->getContainer()->get('woocommerce.payment-gateways');
            $this->getContainer()->get('woocommerce.product');
            $this->getContainer()->get('woocommerce.product-cat');
            $this->getContainer()->get('woocommerce.query');
            $this->getContainer()->get('woocommerce.routing');
            $this->getContainer()->get('woocommerce.script-loader');
            $this->getContainer()->get('woocommerce.shipping');
            $this->getContainer()->get('woocommerce.shortcodes');
            $this->getContainer()->get('woocommerce.stores');
            $this->getContainer()->get('woocommerce.template-filters');
            $this->getContainer()->get('woocommerce.template-hooks');
            $this->getContainer()->get('woocommerce.views.template-loader');
        });

        add_action('init', function () { add_theme_support('woocommerce'); }, 1);
    }

    /**
     * R??cup??ration de la classe de rappel d'un service.
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
        $this->registerCart();
        $this->registerCheckout();
        $this->registerForm();
        $this->registerFunctions();
        $this->registerMail();
        $this->registerMetabox();
        $this->registerOrder();
        $this->registerPaymentGateway();
        $this->registerProduct();
        $this->registerProductCat();
        $this->registerQuery();
        $this->registerRouting();
        $this->registerScriptLoader();
        $this->registerShipping();
        $this->registerShortcodes();
        $this->registerStore();
        $this->registerTemplateFilters();
        $this->registerTemplateHooks();
        $this->registerViewer();
        $this->registerViewsTemplateLoader();
    }

    /**
     * D??claration du service de gestion du panier.
     *
     * @return void
     */
    public function registerCart(): void
    {
        $this->getContainer()->share('woocommerce.cart', function (): CartContract {
            $concrete = $this->getConcrete('cart');

            /** @var CartContract $instance */
            $instance = $concrete instanceof CartContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('cart', []))->parse();
        });
    }

    /**
     * D??claration du service de gestion du processus de commande.
     *
     * @return void
     */
    public function registerCheckout(): void
    {
        $this->getContainer()->share('woocommerce.checkout', function (): CheckoutContract {
            $concrete = $this->getConcrete('checkout');

            /** @var CheckoutContract $instance */
            $instance = $concrete instanceof CheckoutContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.checkout', []))->parse();
        });
    }

    /**
     * D??claration du service de gestion des formulaires.
     *
     * @return void
     */
    public function registerForm(): void
    {
        $this->getContainer()->share('woocommerce.form', function (): FormContract {
            $concrete = $this->getConcrete('form');

            /** @var FormContract $instance */
            $instance = $concrete instanceof FormContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.form', []))->parse();
        });
    }

    /**
     * D??claration du service des fonctions utiles.
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
     * D??claration du service de debug mail.
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
     * D??claration du service de gestion backoffice des m??tadonn??es produit.
     *
     * @return void
     */
    public function registerMetabox(): void
    {
        $this->getContainer()->share('woocommerce.metabox-product', function (): MetaboxProductContract {
            $concrete = $this->getConcrete('metabox-product');

            /** @var MetaboxProductContract $instance */
            $instance = $concrete instanceof MetaboxProductContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set('woocommerce.metabox.product', []);
        });
    }

    /**
     * D??claration du service de gestion des commandes.
     *
     * @return void
     */
    public function registerOrder(): void
    {
        $this->getContainer()->share('woocommerce.order', function (): OrderContract {
            $concrete = $this->getConcrete('order');

            /** @var OrderContract $instance */
            $instance = $concrete instanceof OrderContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });
    }

    /**
     * D??claration du service de gestion des produits.
     *
     * @return void
     */
    public function registerPaymentGateway(): void
    {
        $this->getContainer()->share('woocommerce.payment-gateways', function (): PaymentGatewaysContract {
            $concrete = $this->getConcrete('payment-gateways');

            /** @var PaymentGatewaysContract $instance */
            $instance = $concrete instanceof PaymentGatewaysContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.payment-gateways', []))->parse();
        });

        $this->getContainer()->share('woocommerce.payment-gateway.bacs', function (): PaymentGatewayBacsContract {
            $concrete = $this->getConcrete('payment-gateway.bacs');

            /** @var PaymentGatewayBacsContract $instance */
            $instance = $concrete instanceof PaymentGatewayBacsContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.cheque', function (): PaymentGatewayChequeContract {
            $concrete = $this->getConcrete('payment-gateway.cheque');

            /** @var PaymentGatewayChequeContract $instance */
            $instance = $concrete instanceof PaymentGatewayChequeContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.cod', function (): PaymentGatewayCodContract {
            $concrete = $this->getConcrete('payment-gateway.cod');

            /** @var PaymentGatewayCodContract $instance */
            $instance = $concrete instanceof PaymentGatewayCodContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.custom', function (): PaymentGatewayCustomContract {
            $concrete = $this->getConcrete('payment-gateway.custom');

            /** @var PaymentGatewayCustomContract $instance */
            $instance = $concrete instanceof PaymentGatewayCustomContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });

        $this->getContainer()->share('woocommerce.payment-gateway.paypal', function (): PaymentGatewayPaypalContract {
            $concrete = $this->getConcrete('payment-gateway.paypal');

            /** @var PaymentGatewayPaypalContract $instance */
            $instance = $concrete instanceof PaymentGatewayPaypalContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });
    }

    /**
     * D??claration du service de gestion des produits.
     *
     * @return void
     */
    public function registerProduct(): void
    {
        $this->getContainer()->share('woocommerce.product', function (): ProductContract {
            $concrete = $this->getConcrete('product');

            /** @var ProductContract $instance */
            $instance = $concrete instanceof ProductContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager);
        });
    }

    /**
     * D??claration du gestionnaire de cat??gories de produits.
     *
     * @return void
     */
    public function registerProductCat(): void
    {
        $this->getContainer()->share('woocommerce.product-cat', function (): ProductCatContract {
            $concrete = $this->getConcrete('product-cat');

            /** @var ProductCatContract $instance */
            $instance = $concrete instanceof ProductCatContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.product-cat', []))->parse();
        });
    }

    /**
     * D??claration du service de gestion des requ??tes de r??cup??ration des produits.
     *
     * @return void
     */
    public function registerQuery(): void
    {
        $this->getContainer()->share('woocommerce.query', function (): QueryContract {
            $concrete = $this->getConcrete('query');

            /** @var QueryContract $instance */
            $instance = $concrete instanceof QueryContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.query', []))->parse();
        });

        $this->getContainer()->add('woocommerce.query.product', function (): ?QueryProductContract {
            /** @var QueryProductContract $concrete */
            $concrete = $this->getConcrete('query.product');

            if (is_string($concrete)) {
                QueryPost::setBuiltInClass('product', $concrete);
                QueryPost::setBuiltInClass('product_variation', $concrete);

                $concrete = new $concrete();
            } else {
                $classname = get_class($concrete);

                QueryPost::setBuiltInClass('product', $classname);
                QueryPost::setBuiltInClass('product_variation', $classname);
            }

            return $concrete;
        });

        $this->getContainer()->add('woocommerce.query.order', function (): ?QueryOrderContract {
            /** @var QueryOrderContract $concrete */
            $concrete = $this->getConcrete('query.order');

            if (is_string($concrete)) {
                QueryPost::setBuiltInClass('shop_order', $concrete);

                $concrete = new $concrete();
            } else {
                QueryPost::setBuiltInClass('shop_order', get_class($concrete));
            }

            return $concrete;
        });
    }

    /**
     * D??claration du service de gestion des routes.
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
     * D??claration du service de chargement des scripts JS et Feuilles de styles CSS.
     *
     * @return void
     */
    public function registerScriptLoader(): void
    {
        $this->getContainer()->share('woocommerce.script-loader', function (): ScriptLoaderContract {
            $concrete = $this->getConcrete('script-loader');

            /** @var ScriptLoaderContract $instance */
            $instance = $concrete instanceof ScriptLoaderContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.script-loader', []))->parse();
        });
    }

    /**
     * D??claration du service de gestion de l'exp??dition.
     *
     * @return void
     */
    public function registerShipping(): void
    {
        $this->getContainer()->share('woocommerce.shipping', function (): ShippingContract {
            $concrete = $this->getConcrete('shipping');

            /** @var ShippingContract $instance */
            $instance = $concrete instanceof ShippingContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.shipping', []))->parse();
        });
    }

    /**
     * D??claration du service de gestion des shortcodes.
     *
     * @return void
     */
    public function registerShortcodes(): void
    {
        $this->getContainer()->share('woocommerce.shortcodes', function (): ShortcodesContract {
            $concrete = $this->getConcrete('shortcodes');

            /** @var ShortcodesContract $instance */
            $instance = $concrete instanceof ShortcodesContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.shortcodes', []))->parse();
        });
    }

    /**
     * D??claration des services de gestion des magasins.
     *
     * @return void
     */
    public function registerStore(): void
    {
        $this->getContainer()->share('woocommerce.stores', function (): StoresContract {
            $concrete = $this->getConcrete('stores');

            /** @var StoresContract $instance */
            $instance = $concrete instanceof StoresContract ? $concrete : new $concrete();

            add_action('admin_init', function () use ($instance) {
                /* */
                if ($instance->collect()->firstWhere('admin', '=', true)) {
                    Metabox::add('woocommerce.store.options', [
                        'title' => __('Boutiques woocommerce', 'tify'),
                    ])
                        ->setScreen('tify_options@options')
                        ->setContext('tab');
                }
                /**/
            }, 999999);

            add_action('woocommerce_get_shop_page_id', function ($page_id) use ($instance) {
                if (is_singular() && (in_array(get_the_ID(), $instance->getPageIds()))) {
                    $page_id = get_the_ID();
                }

                return $page_id;
            });

            return $instance->setManager($this->manager)->set(config('woocommerce.store', []));
        });

        $this->getContainer()->add('woocommerce.store-factory', function (): StoreFactoryContract {
            $concrete = $this->getConcrete('store-factory');

            /** @var StoreFactoryContract $instance */
            return $instance = $concrete instanceof StoreFactoryContract ? $concrete : new $concrete();
        });
    }

    /**
     * D??claration du service des filtres d'arguments de template Woocommerce.
     *
     * @return void
     */
    public function registerTemplateFilters(): void
    {
        $this->getContainer()->share('woocommerce.template-filters', function (): TemplateFiltersContract {
            $concrete = $this->getConcrete('template-filters');

            /** @var TemplateFiltersContract $instance */
            $instance = $concrete instanceof TemplateFiltersContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.template-filters', []))->parse();
        });
    }

    /**
     * D??claration du service de gestion des fonctions d'accroche des templates de Woocommerce.
     *
     * @return void
     */
    public function registerTemplateHooks(): void
    {
        $this->getContainer()->share('woocommerce.template-hooks', function (): TemplateHooksContract {
            $concrete = $this->getConcrete('template-hooks');

            /** @var TemplateHooksContract $instance */
            $instance = $concrete instanceof TemplateHooksContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.template-hooks', []))->parse();
        });
    }

    /**
     * D??claration du service de surcharge des ??l??ments de template Woocommerce.
     *
     * @return void
     */
    public function registerViewer(): void
    {
        $this->getContainer()->share('woocommerce.viewer', function () {
            $cinfo = class_info($this);
            $default_dir = $cinfo->getDirname() . '/Resources/views';

            return View::getPlatesEngine([
                'directory'    => is_dir($default_dir) ? $default_dir : null,
                'override_dir' => (($override_dir = config('woocommerce.viewer.override_dir')) && is_dir($override_dir))
                    ? $override_dir
                    : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname()),
            ]);
        });
    }

    /**
     * D??claration du service de gestion du chargement des templates Woocommerce.
     *
     * @return void
     */
    public function registerViewsTemplateLoader(): void
    {
        $this->getContainer()->share('woocommerce.views.template-loader', function (): TemplateLoaderContract {
            $concrete = $this->getConcrete('views.template-loader');

            /** @var TemplateLoaderContract $instance */
            $instance = $concrete instanceof TemplateLoaderContract ? $concrete : new $concrete();

            return $instance->setManager($this->manager)->set(config('woocommerce.template-loader', []));
        });
    }
}