<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Views;

use tiFy\Plugins\Woocommerce\Contracts\Template as TemplateContract;

/**
 * @see Woocommerce/includes/wc-template-functions.php
 */
class Template implements TemplateContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_filter('woocommerce_breadcrumb_defaults', [$this, 'woocommerce_breadcrumb_defaults']);
    }

    /**
     * Court-circuitage des attributs du fil d'Ariane
     *
     * @param array $args {
     *      @var $delimiter, '&nbsp;&#47;&nbsp;' par défaut.
     *      @var $wrap_before, '<nav class="woocommerce-breadcrumb">' par défaut.
     *      @var $wrap_after, '</nav>' par défaut.
     *      @var $before, '' par défaut.
     *      @var $after, '' par défaut.
     *      @var $home, _x( 'Home', 'breadcrumb', 'woocommerce' ) par défaut.
     * }
     *
     * @return array
     */
    public function woocommerce_breadcrumb_defaults($args = []) : array
    {
        return $args;
    }
}