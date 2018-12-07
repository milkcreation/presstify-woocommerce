<?php

use App\Woocommerce\Assets\Assets;
use App\Woocommerce\Cart\Cart;
use App\Woocommerce\Checkout\Checkout;
use App\Woocommerce\Form\Form;
use App\Woocommerce\Mail\Mail;
use App\Woocommerce\Metabox\Product;
use App\Woocommerce\Multishop\Multishop;
use App\Woocommerce\Multishop\Factory;
use App\Woocommerce\Order\Order;
use App\Woocommerce\Query\Query;
use App\Woocommerce\Routing\Routing;
use App\Woocommerce\Shipping\Shipping;
use App\Woocommerce\Shortcodes\Shortcodes;

return [
    // CHARGEMENT DES SCRIPTS
    'assets'          => [
        /// Chargement des Feuilles de styles natives Woocommerce
        'wc_styles'  => [
            'woocommerce-layout'      => false,
            'woocommerce-smallscreen' => false,
            'woocommerce-general'     => false
        ],
        /// Chargement des Scripts JS natifs Woocommerce
        'wc_scripts' => [
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
        ]
    ],
    // COMMANDE
    'checkout'        => [
        'min_purchase' => [
            'rate'   => 99,
            'notice' => __('Désolé, le montant minimum des commandes est fixé à %s', 'tify')
        ]
    ],
    // FORMULAIRE
    /** @see woocommerce_form_field() */
    'form'            => [
        'tify_select_js_country' => false,
        'add_address_fields'     => [
            'address_3'      => [
                'label'    => __('Adresse ligne 3', 'tify'),
                'required' => true,
                'priority' => 55,
                'admin'    => [
                    'billing'  => [
                        'label'       => __('Adresse ligne 3', 'theme'),
                        'description' => 'Ceci est la description du champ "ligne adresse 3"',
                        'before'      => 'city'
                    ],
                    'shipping' => false
                ]
            ],
            'portable_phone' => [
                'label'    => __('Téléphone portable', 'tify'),
                'priority' => 95
            ]
        ],
        'billing'                => [
            'first_name' => [
                'priority' => 20
            ],
            'last_name'  => [
                'priority' => 10
            ],
            'company'    => [
                'priority' => 30
            ],
            'address_1'  => [
                'priority' => 40
            ],
            'address_2'  => [
                'priority' => 50
            ],
            'city'       => [
                'priority' => 80
            ],
            'postcode'   => [
                'priority' => 70
            ],
            'country'    => [
                'priority' => 60
            ],
            'state'      => [
                'priority' => 110
            ],
            'phone'      => [
                'priority' => 90
            ],
            'email'      => [
                'priority' => 100
            ]
        ],
        'shipping'               => [
            'first_name'     => [
                'priority' => 20
            ],
            'last_name'      => [
                'priority' => 10
            ],
            'company'        => [
                'priority' => 30
            ],
            'address_1'      => [
                'priority' => 40
            ],
            'address_2'      => [
                'priority' => 50
            ],
            'city'           => [
                'priority' => 80
            ],
            'postcode'       => [
                'priority' => 70
            ],
            'country'        => [
                'priority' => 60
            ],
            'state'          => [
                'priority' => 90
            ],
            'portable_phone' => false
        ],
        'checkout'               => [
            // Surcharge formulaire facturation au moment de la commande
            'billing'  => [
                'last_name' => [
                    'priority' => 25
                ]
            ],
            // Surcharge formulaire livraison au moment de la commande
            'shipping' => [
                'company' => [
                    'priority' => 100
                ]
            ],
            'account'  => [
                'username'   => [
                    'class' => ['WcInput']
                ],
                'password'   => [
                    'class' => ['WcInput', 'WcInput--password']
                ],
                'password-2' => [
                    'class' => ['WcInput', 'WcInput--password']
                ]
            ],
            'order'    => [
                'comments' => [
                    'class' => ['WcTextarea']
                ]
            ]
        ]
    ],
    // BOUTIQUE MULTIPLE
    'multishop'       => [
        'hifi'     => [
            'title' => __('Image et son', 'theme')
        ],
        'security' => [
            'title' => __('Securité', 'theme')
        ]
    ],
    // PLATEFORMES DE PAIEMENT
    'payment_gateway' => [
        'WC_Gateway_BACS'   => true,
        'WC_Gateway_Cheque' => true,
        'WC_Gateway_COD'    => true,
        'WC_Gateway_Paypal' => true,
        'Custom'            => true
    ],
    'routing'         => [
        'shop_homepage' => [
            'admin' => [
                'title' => __('Page d\'accueil de la boutique', 'theme'),
                'desc'  => __('Ceci détermine la page d\'accueil de votre boutique - c\'est l\'emplacement du diaporama, des catégories de produits et des mises en avant de nouveautés, promotions et marques.', 'theme'),
            ]
        ]
    ],
    // GESTION DES SHORTCODES
    /// Mettre à false pour désactiver
    'shortcodes'      => [
        'woocommerce_cart'           => true,
        'woocommerce_checkout'       => true,
        'woocommerce_order_tracking' => true,
        'woocommerce_my_account'     => true
    ],
    // ACCROCHAGE / DECROCHAGE / RE-ORDONNANCEMENT DES ELEMENTS DE TEMPLATES
    'template-hooks'  => [

    ],
    // Chargement des templates avec le moteur de gabarit PHP Plates
    /// Définition du service à charger
    'template_loader' => '',
    // SERVICES
    'providers'       => [
        'assets'            => Assets::class,
        'cart'              => Cart::class,
        'checkout'          => Checkout::class,
        'form'              => Form::class,
        'mail'              => Mail::class,
        'metabox.product'   => Product::class,
        'multishop'         => Multishop::class,
        'multishop.factory' => Factory::class,
        'order'             => Order::class,
        'query'             => Query::class,
        'routing'           => Routing::class,
        'shipping'          => Shipping::class,
        'shortcodes'        => Shortcodes::class,
    ]
];