<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Store;

class StoreOptionsMetabox extends AbstractStoreMetabox
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_filter('pre_option_woocommerce_shop_page_display', function ($value) {
            if (is_customize_preview()) {
                return $value;
            }
            return $value;
        }, 10, 2);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return (string)$this->woocommerce()->viewer('store/options-general', $this->all());
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function sanitize_tify_wc_page_display($value)
    {
        // Récupération de la catégorie affichée par la boutique
        $cat = (int)get_option('tify_wc_'. $this->store()->getName() .'_cat', 0);

        // Récupération de la catégorie liée à l'affichage de la page
        if ($page_display_cat = (int)get_option('tify_wc_'. $this->store()->getName() .'_page_display_cat', 0) ) {
            if ($cat !== $page_display_cat) {
                delete_term_meta($page_display_cat, 'display_type');
            }
        }

        update_option('tify_wc_'. $this->store()->getName() .'_page_display_cat', $cat);
        update_term_meta($cat, 'display_type', $value);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function settings()
    {
        return [
            'tify_wc_'. $this->store()->getName() .'_hook',
            'tify_wc_'. $this->store()->getName() .'_cat',
            'tify_wc_'. $this->store()->getName() .'_page_display' => [
                'sanitize_callback' => [$this, 'sanitize_tify_wc_page_display']
            ],
            'tify_wc_multi_default'
        ];
    }
}