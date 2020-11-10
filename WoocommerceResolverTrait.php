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
        return $this->resolve('form');
    }

    /**
     * @inheritdoc
     */
    public function product()
    {
        return $this->resolve('product');
    }

    /**
     * @inheritdoc
     */
    public function resolve($alias, ...$args)
    {
        return app()->get("woocommerce.{$alias}", $args);
    }

    /**
     * @inheritdoc
     */
    public function routing()
    {
        return $this->resolve('routing');
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