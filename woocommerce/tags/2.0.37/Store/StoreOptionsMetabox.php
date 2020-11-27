<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Store;

use tiFy\Contracts\Metabox\MetaboxDriver;

class StoreOptionsMetabox extends AbstractStoreMetabox
{
    /**
     * @inheritDoc
     */
    public function boot(): MetaboxDriver
    {
        parent::boot();

        add_filter('pre_option_woocommerce_shop_page_display', function ($value) {
            if (is_customize_preview()) {
                return $value;
            }
            return $value;
        }, 10, 2);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->woocommerce()->viewer('store/options-general', $this->all());
    }
}