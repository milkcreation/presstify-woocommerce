<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Woocommerce\Contracts\Form;
use tiFy\Plugins\Woocommerce\Contracts\Routing;

trait WoocommerceResolverTrait
{
    /**
     * {@inheritdoc}
     *
     * @return Form
     */
    public function form()
    {
        return app()->get('woocommerce.form');
    }

    /**
     * {@inheritdoc}
     *
     * @return Routing
     */
    public function routing()
    {
        return app()->get('woocommerce.routing');
    }

    /**
     * {@inheritdoc}
     *
     * @return ViewController|ViewEngine
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