<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Product;

use tiFy\Support\Proxy\Asset;
use tiFy\Plugins\Woocommerce\Contracts\Product as ProductContract;
use tiFy\Plugins\Woocommerce\Contracts\QueryProduct;
use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;
use WC_Product;
use WP_Post;

class Product implements ProductContract
{
    use WoocommerceResolverTrait;

    /**
     * Instance des produits.
     * @var QueryProduct[]
     */
    protected $products = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('wp', function () {
            if (is_product() && ($product = $this->get())) {
                Asset::setDataJs('wc', ['product' => $this->getDatas($product)]);
            }
        }, 99);
    }

    /**
     * Récupération d'un produit.
     *
     * @param null|int
     *
     * @return null|QueryProduct
     */
    public function get(?int $product_id = null): ?QueryProduct
    {
        if ($product_id) {
        } else {
            global $product, $post;

            if ($product instanceof WC_Product) {
                $product_id = $product->get_id();
            } elseif ($post instanceof WP_Post && ($post->post_type === 'product')) {
                $product_id = $post->ID;
            } else {
                return null;
            }
        }

        if ($product_id) {
            return $this->products[$product_id] =
                $this->products[$product_id] ?? $this->resolve('query.product', $product_id);
        } else {
            return null;
        }
    }

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @param QueryProduct $product
     *
     * @return array
     */
    public function getDatas(QueryProduct $product): array
    {
        $datas = [];

        $datas['id'] = $product->getId();

        if ($product->isVariable()) {
            $datas['type']  = 'variable';
            $datas['infos'] = [];
            if ($children = $product->getChildren()) {
                foreach ($children as $child) {
                    $datas['infos'][] = $this->getInfos($child);
                }
            }
        } elseif ($product->isSimple()) {
            $datas['type']  = 'simple';
            $datas['infos'] = $this->getInfos($product);
        }

        return $datas;
    }

    /**
     * Récupération de la liste des données associées à un produit.
     *
     * @param QueryProduct $product
     *
     * @return array
     */
    public function getInfos(QueryProduct $product): array
    {
        $infos = [];

        if ($product->isVariation()) {
            $infos = [
                'attributes'            => $product->getProduct()->get_variation_attributes(),
                'variation_description' => wc_format_content($product->getProduct()->get_description()),
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
            'currency'              => get_woocommerce_currency_symbol(),
            'price_with_tax'        => $product->getPriceIncludingTax(),
            'price_without_tax'     => $product->getPriceExcludingTax()
        ], $infos);
    }
}