<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\Plugins\Woocommerce\Contracts\Form;

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
}