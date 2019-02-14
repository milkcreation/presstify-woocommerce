<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\Plugins\Woocommerce\Contracts\Woocommerce as WoocommerceContract;

/**
 * Class Woocommerce
 *
 * @desc Extension PresstiFy de court-circuitage et de fonctionnalités complémentaires woocommerce.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @author Julien Picard <julien@tigreblanc.fr>
 * @package tiFy\Plugins\Woocommerce
 * @version 2.0.3
 *
 * @see https://docs.woocommerce.com/wc-apidocs/index.html
 *
 * USAGE :
 * Activation
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Woocommerce\WoocommerceServiceProvider à la liste des fournisseurs de
 * services.
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
 * Configuration
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier woocommerce.php
 * @see /vendor/presstify-plugins/woocommerce/Resources/config/woocommerce.php
 */
class Woocommerce implements WoocommerceContract
{
    use WoocommerceResolverTrait;
}
