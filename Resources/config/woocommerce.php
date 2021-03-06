<?php

use App\Woocommerce\Assets;
use App\Woocommerce\Cart;
use App\Woocommerce\Checkout;
use App\Woocommerce\Form;
use App\Woocommerce\Mail;
use App\Woocommerce\Product;
use App\Woocommerce\Factory;
use App\Woocommerce\Order;
use App\Woocommerce\Query;
use App\Woocommerce\Routing;
use App\Woocommerce\Shipping;
use App\Woocommerce\Shortcodes;
use App\Woocommerce\Stores;
use App\Woocommerce\Views\Template;
use App\Woocommerce\Views\TemplateHooks;
use App\Woocommerce\Views\TemplateLoader;

return [
    /**
     * Gestion d'un montant minimum de commande.
     * @var array
     */
    'checkout'        => [
        'min' => [
            'rate'   => 99,
            'notice' => __('Désolé, le montant minimum des commandes est fixé à %s', 'tify'),
        ],
    ],

    /**
     * Gestion des formulaires Woocommerce.
     * Surcharge des formulaires existants (billing, shipping, checkout)
     * Ajout de champs personnalisés sur les formulaires billing et shipping.
     *
     * @see woocommerce_form_field()
     *
     * Champs additionnels : select_js, select_js_country
     *
     * @var array
     */
    'form'            => [
        'add_address_fields' => [
            'address_3'      => [
                'label'    => __('Adresse ligne 3', 'tify'),
                'required' => true,
                'priority' => 55,
                'admin'    => [
                    'billing'  => [
                        'label'       => __('Adresse ligne 3', 'tify'),
                        'description' => 'Ceci est la description du champ "ligne adresse 3"',
                        'before'      => 'city',
                    ],
                    'shipping' => false,
                ],
            ],
            'portable_phone' => [
                'label'    => __('Téléphone portable', 'tify'),
                'priority' => 95,
            ],
            'civility'       => [
                'label'   => __('Civilité', 'tify'),
                'type'    => 'select',
                'options' => ['madame' => __('Mme', 'tify'), 'monsieur' => __('M.', 'tify')],
                'admin'   => [
                    'billing'  => [
                        'type'    => 'select',
                        'options' => ['madame' => __('Mme', 'tify'), 'monsieur' => __('M.', 'tify')],
                        'before'  => 'first_name',
                    ],
                    'shipping' => [
                        'type'    => 'select',
                        'options' => ['madame' => __('Mme', 'tify'), 'monsieur' => __('M.', 'tify')],
                        'before'  => 'first_name',
                    ],
                ],
            ],
        ],
        'billing'            => [
            'first_name' => [
                'priority' => 20,
            ],
            'last_name'  => [
                'priority' => 10,
            ],
            'company'    => [
                'priority' => 30,
            ],
            'address_1'  => [
                'priority' => 40,
            ],
            'address_2'  => [
                'priority' => 50,
            ],
            'city'       => [
                'priority' => 80,
            ],
            'postcode'   => [
                'priority' => 70,
            ],
            'country'    => [
                'priority' => 60,
                'type'     => 'select_js_country',
            ],
            'state'      => [
                'priority' => 110,
                'type'     => 'select_js_state',
            ],
            'phone'      => [
                'priority' => 90,
            ],
            'email'      => [
                'priority' => 100,
            ],
            'civility'   => [
                'required' => true,
                'priority' => 15,
                'type'     => 'select_js',
            ],
        ],
        'shipping'           => [
            'first_name'     => [
                'priority' => 20,
            ],
            'last_name'      => [
                'priority' => 10,
            ],
            'company'        => [
                'priority' => 30,
            ],
            'address_1'      => [
                'priority' => 40,
            ],
            'address_2'      => [
                'priority' => 50,
            ],
            'city'           => [
                'priority' => 80,
            ],
            'postcode'       => [
                'priority' => 70,
            ],
            'country'        => [
                'priority' => 60,
                'type'     => 'select_js_country',
            ],
            'state'          => [
                'priority' => 90,
            ],
            'portable_phone' => false,
        ],
        'checkout'           => [
            // Surcharge formulaire facturation au moment de la commande
            'billing'  => [
                'last_name' => [
                    'priority' => 25,
                ],
            ],
            // Surcharge formulaire livraison au moment de la commande
            'shipping' => [
                'company' => [
                    'priority' => 100,
                ],
            ],
            'account'  => [
                'username'   => [
                    'class' => ['WcInput'],
                ],
                'password'   => [
                    'class' => ['WcInput', 'WcInput--password'],
                ],
                'password-2' => [
                    'class' => ['WcInput', 'WcInput--password'],
                ],
            ],
            'order'    => [
                'comments' => [
                    'class' => ['WcTextarea'],
                ],
            ],
        ],
    ],

    /**
     * Déclaration des plateformes de paiement.
     *
     * @todo
     *
     * @var array
     */
    'payment_gateway' => [
        'WC_Gateway_BACS'   => true,
        'WC_Gateway_Cheque' => true,
        'WC_Gateway_COD'    => true,
        'WC_Gateway_Paypal' => true,
        'Custom'            => true,
    ],

    /* Fournisseurs de services
     * @var array
     */
    'providers'       => [
        'assets'                => Assets::class,
        'cart'                  => Cart::class,
        'checkout'              => Checkout::class,
        'form'                  => Form::class,
        'mail'                  => Mail::class,
        'metabox-product'       => Product::class,
        'order'                 => Order::class,
        'query'                 => Query::class,
        'routing'               => Routing::class,
        'shipping'              => Shipping::class,
        'shortcodes'            => Shortcodes::class,
        'stores'                => Stores::class,
        'store-factory'         => Factory::class,
        'views.template'        => Template::class,
        'views.template-hooks'  => TemplateHooks::class,
        'views.template-loader' => TemplateLoader::class,
    ],

    /**
     * Déclaration et accrochage de routes personnalisées à Woocommerce.
     *
     * @var array
     */
    'routing'         => [
        'shop_homepage' => [
            'admin' => [
                'title' => __('Page d\'accueil de la boutique', 'tify'),
                'desc'  => __('Ceci détermine la page d\'accueil de votre boutique - c\'est l\'emplacement du diaporama, des catégories de produits et des mises en avant de nouveautés, promotions et marques.',
                    'tify'),
            ],
        ],
    ],

    /**
     * Chargement des scripts (Feuille de styles CSS et scripts JS).
     * false = désactivation du style/script.
     *
     * @var array
     */
    'script-loader'          => [
        /// Chargement des Feuilles de styles natives Woocommerce
        'css'  => [
            'woocommerce-layout'      => false,
            'woocommerce-smallscreen' => false,
            'woocommerce-general'     => false,
        ],
        /// Chargement des Scripts JS natifs Woocommerce
        'js' => [
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
        ],
    ],

    /**
     * Gestion des shortcodes Woocommerce.
     * false = désactivation du shortcode.
     *
     * @var array
     */
    'shortcodes'      => [
        'woocommerce_cart'           => false,
        'woocommerce_checkout'       => true,
        'woocommerce_order_tracking' => true,
        'woocommerce_my_account'     => true,
    ],
    /**
     * Gestion de magasins.
     *
     * @var array
     */
    'store'           => [
        'hifi'     => [
            'title' => __('Image et son', 'tify'),
        ],
        'security' => [
            'title' => __('Securité', 'tify'),
        ],
    ],

    /**
     * Gestion des hooks wooocommerce (accrochage, décrochage, réordonnnancement).
     *
     * @var array
     */
    'template-hooks'  => [
        'woocommerce_before_shop_loop_item'       => [
            'woocommerce_template_loop_product_link_open'  => 1,
            'woocommerce_template_loop_product_link_close' => 2,
        ],
        'woocommerce_before_shop_loop_item_title' => [
            'woocommerce_show_product_loop_sale_flash'    => false,
            'woocommerce_template_loop_product_thumbnail' => 10,
        ],
        'woocommerce_after_shop_loop_item_title'  => [
            'woocommerce_template_loop_price' => false,
        ],
    ],

    /**
     * Chargement des templates avec le moteur de gabarit PHP Plates.
     * Définition du service à charger.
     *
     * @var array
     */
    /*'template-loader' => [
        'viewer' => new ViewEngine()
    ],*/

    /**
     * Attributs de configuration du gestionnaire d'affichage de gabarits.
     * @see \tiFy\Contracts\View\ViewEngine
     *
     * @var array
     */
    'viewer'          => [
        'override_dir' => get_stylesheet_directory() . '/views/layout/wc',
    ],
];