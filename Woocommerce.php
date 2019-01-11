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
        add_action(
            'init',
            function () {
                add_theme_support('woocommerce');
            },
            1
        );
    }
}
