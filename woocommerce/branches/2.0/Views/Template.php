<?php

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
        // Court-circuitage des attributs du fil d'Ariane
        add_filter('woocommerce_breadcrumb_defaults', [$this, 'woocommerce_breadcrumb_defaults']);
    }

    /**
     * BREADCRUMB
     */
    /**
     * Court-circuitage des attributs du fil d'Ariane
     *
     * @param array $args
     * array(
     * 'delimiter'   => '&nbsp;&#47;&nbsp;',
     * 'wrap_before' => '<nav class="woocommerce-breadcrumb">',
     * 'wrap_after'  => '</nav>',
     * 'before'      => '',
     * 'after'       => '',
     * 'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
     * )
     *
     * @return array
     */
    public function woocommerce_breadcrumb_defaults($args = [])
    {
        return $args;
    }
}