<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Shortcodes;

use tiFy\Plugins\Woocommerce\{Contracts\Shortcodes as ShortcodesContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

/**
 * @see https://docs.woocommerce.com/document/woocommerce-shortcodes/
 * @see https://docs.woocommerce.com/document/shortcodes/
 */
class Shortcodes extends ParamsBag implements ShortcodesContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        // Désactivation de l'éxecution du shortcode Woocommerce dans le contenu de page.
        add_filter('pre_do_shortcode_tag', function ($output, $tag, $attr,$m) {
            if (!in_array($tag, $this->keys()) || !in_the_loop() || $this->get($tag)) {
                return $output;
            } else {
                return null;
            }
        }, 10, 4);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function disable(string $tag): void
    {
        if (in_array($tag, $this->keys())) {
            $this->set($tag, false);
        }
    }

    /**
     * @inheritDoc
     */
    public function doing(string $tag, array $attrs = []): ?string
    {
        if (!preg_match('/^woocommerce_(.*)/', $tag)) {
            $tag = 'woocommerce_' . $tag;
        }

        if (in_array($tag, $this->keys())) {
            $map = [
                'woocommerce_order_tracking' => 'WC_Shortcodes::order_tracking',
                'woocommerce_cart'           => 'WC_Shortcodes::cart',
                'woocommerce_checkout'       => 'WC_Shortcodes::checkout',
                'woocommerce_my_account'     => 'WC_Shortcodes::my_account',
            ];

            if (isset($map[$tag])) {
                return call_user_func($map[$tag], $attrs);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function parse(): ShortcodesContract
    {
        parent::parse();

        $this->set(array_merge([
            'woocommerce_cart'           => true,
            'woocommerce_checkout'       => true,
            'woocommerce_order_tracking' => true,
            'woocommerce_my_account'     => true
        ], $this->all()));

        foreach ($this->all() as $shortcode => $enabled) {
            if (!$enabled) {
                $this->disable($shortcode);
            }
        }

        return $this;
    }
}