<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Woocommerce\Contracts\WoocommerceResolverTrait as WoocommerceResolverTraitContract;

/**
 * Trait WoocommerceResolverTrait
 * @package tiFy\Plugins\Woocommerce
 *
 * @mixin WoocommerceResolverTraitContract
 */
trait WoocommerceResolverTrait
{
    /**
     * @inheritdoc
     */
    public function form()
    {
        return app()->get('woocommerce.form');
    }

    /**
     * @inheritdoc
     */
    public function query_product($product = null)
    {
        return app()->get('woocommerce.query.product', [$product]);
    }

    /**
     * @inheritdoc
     */
    public function query_products($wp_query = null)
    {
        return app()->get('woocommerce.query.products', [$wp_query]);
    }

    /**
     * @inheritdoc
     */
    public function routing()
    {
        return app()->get('woocommerce.routing');
    }

    /**
     * @inheritdoc
     */
    public function viewer($view = null, $data = [])
    {
        /** @var ViewEngine $viewer */
        $viewer = app()->get('woocommerce.viewer');

        if (func_num_args() === 0) :
            return $viewer;
        endif;

        return $viewer->make("_override::{$view}", $data);
    }
}