<?php

namespace tiFy\Plugins\Woocommerce;

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
}