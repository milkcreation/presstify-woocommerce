<?php

namespace tiFy\Plugins\Woocommerce\Product;

use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;
use tiFy\Plugins\Woocommerce\Contracts\QueryProduct;

class Product
{
    use WoocommerceResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('wp', function () {
            if (is_product()) :
                assets()->setDataJs('wc', ['product' => $this->getDatas($this->query_product())]);
            endif;
        }, 99);
    }

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @param QueryProduct $product
     *
     * @return array
     */
    public function getDatas(QueryProduct $product)
    {
        $datas = [];

        if ($product->isVariable()) :
            $datas['type'] = 'variable';
            $datas['infos'] = [];
            foreach($product->getVariations() as $variation) {
                $datas['infos'][] = $this->getInfos($variation);
            }
        elseif ($product->isSimple()) :
            $datas['type'] = 'simple';
            $datas['infos'] = $this->getInfos($product);
        endif;

        return $datas;
    }

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @param QueryProduct $product
     *
     * @return array
     */
    public function getInfos(QueryProduct $product)
    {
        $infos = [];

        if ($product->isVariation()) {
            $infos = [
                'attributes' => $product->getProduct()->get_variation_attributes(),
                'variation_description' => wc_format_content($product->getProduct()->get_description() ),
                'variation_id'          => $product->getProduct()->get_id(),
                'variation_is_active'   => $product->getProduct()->variation_is_active(),
                'variation_is_visible'  => $product->getProduct()->variation_is_visible(),
            ];
        }

        return array_merge([
            'availability_html'     => wc_get_stock_html($product->getProduct()),
            'backorders_allowed'    => $product->getProduct()->backorders_allowed(),
            'dimensions'            => $product->getProduct()->get_dimensions(false),
            'dimensions_html'       => wc_format_dimensions($product->getProduct()->get_dimensions(false)),
            'display_price'         => wc_get_price_to_display($product->getProduct()),
            'display_regular_price' => wc_get_price_to_display(
                $product->getProduct(),
                [
                    'price' => $product->getProduct()->get_regular_price()
                ]
            ),
            'image'                 => wc_get_product_attachment_props($product->getProduct()->get_image_id()),
            'image_id'              => $product->getProduct()->get_image_id(),
            'is_downloadable'       => $product->getProduct()->is_downloadable(),
            'is_in_stock'           => $product->getProduct()->is_in_stock(),
            'is_purchasable'        => $product->getProduct()->is_purchasable(),
            'is_sold_individually'  => $product->getProduct()->is_sold_individually() ? 'yes' : 'no',
            'is_virtual'            => $product->getProduct()->is_virtual(),
            'max_qty'               => 0 < $product->getProduct()->get_max_purchase_quantity()
                ? $product->getProduct()->get_max_purchase_quantity() : '',
            'min_qty'               => $product->getProduct()->get_min_purchase_quantity(),
            'price_html'            => '<span class="price">' . $product->getProduct()->get_price_html() . '</span>',
            'sku'                   => $product->getProduct()->get_sku(),
            'description'           => wc_format_content($product->getProduct()->get_description()),
            'weight'                => $product->getProduct()->get_weight(),
            'weight_html'           => wc_format_weight($product->getProduct()->get_weight()),
        ], $infos);
    }
}