<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\Contracts\{
    QueryProducts as QueryProductsContract,
    QueryProduct as QueryProductContract};
use tiFy\Wordpress\{Contracts\QueryPost as QueryPostContract, Query\QueryPost};
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;
use WP_Post;

class QueryProduct extends QueryPost implements QueryProductContract
{
    /**
     * Objets QueryProducts des produits enfants.
     * {@internal Produit variable uniquement}
     * @var null|QueryProductsContract|QueryProductsContract[]
     */
    protected $children;

    /**
     * Prix de vente maximum (avec et sans taxe).
     * @var array
     */
    protected $max_price = [];

    /**
     * Prix de vente minimum (avec et sans taxe).
     * @var array
     */
    protected $min_price;

    /**
     * Instance du produit parent.
     * {@internal Variation uniquement}
     * @var QueryProductContract|false|null
     */
    protected $parent;

    /**
     * Liste des instances de variations associées au produit.
     * {@internal Le produit doit être de type variable}
     * @var null|array|QueryProductsContract|QueryProductContract[]
     */
    protected $variations;

    /**
     * Objet Product Woocommerce.
     * @var WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    protected $wc_product;

    /**
     * Objet Post Wordpress.
     * @var null|WP_Post
     */
    protected $wp_post;

    /**
     * CONSTRUCTEUR.
     *
     * @param WC_Product $wc_product Objet Product Woocommerce.
     *
     * @return void
     */
    public function __construct(WC_Product $wc_product)
    {
        $this->wc_product = $wc_product;

        parent::__construct(get_post($this->wc_product->get_id()));
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductContract|null
     */
    public static function createFromGlobal(): ?QueryPostContract
    {
        global $product, $post;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product($post);
        }

        return $product instanceof WC_Product ? new static($product) : null;
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductContract|null
     */
    public static function createFromId($product_id): ?QueryPostContract
    {
        return (
            $product_id &&
            is_numeric($product_id) &&
            ($wp_product = wc_get_product($product_id)) &&
            ($wp_product instanceof WC_Product)
        )
            ? new static($wp_product) : null;
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        if (is_null($this->children)) {
            $this->children = [];
            if ($this->isVariable() && ($child_ids = $this->getWcProduct()->get_children())) {
                $this->children = QueryProducts::createFromIds($child_ids);
            }
        }
        return $this->children ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getDatas(): array
    {
        $datas = [];

        $datas['id'] = $this->getId();

        if ($this->isVariable()) {
            $datas['type']  = 'variable';
            $datas['infos'] = [];
            foreach ($this->getChildren() as $child) {
                $datas['infos'][] = $child->getInfos();
            }
        } elseif ($this->isSimple()) {
            $datas['type']  = 'simple';
            $datas['infos'] = $this->getInfos();
        }

        return $datas;
    }

    /**
     * @inheritDoc
     */
    public function getInfos(): array
    {
        $infos = [];

        if ($this->isVariation()) {
            $infos = [
                'attributes'            => $this->getWcProduct()->get_variation_attributes(),
                'variation_description' => wc_format_content($this->getWcProduct()->get_description()),
                'variation_id'          => $this->getId(),
                'variation_is_active'   => $this->getWcProduct()->variation_is_active(),
                'variation_is_visible'  => $this->getWcProduct()->variation_is_visible(),
            ];
        }

        return array_merge([
            'availability_html'     => wc_get_stock_html($this->getWcProduct()),
            'backorders_allowed'    => $this->getWcProduct()->backorders_allowed(),
            'dimensions'            => $this->getWcProduct()->get_dimensions(false),
            'dimensions_html'       => wc_format_dimensions($this->getWcProduct()->get_dimensions(false)),
            'display_price'         => wc_get_price_to_display($this->getWcProduct()),
            'display_regular_price' => wc_get_price_to_display($this->getWcProduct(), [
                    'price' => $this->getWcProduct()->get_regular_price()
                ]
            ),
            'image'                 => wc_get_product_attachment_props($this->getWcProduct()->get_image_id()),
            'image_id'              => $this->getWcProduct()->get_image_id(),
            'is_downloadable'       => $this->getWcProduct()->is_downloadable(),
            'is_in_stock'           => $this->getWcProduct()->is_in_stock(),
            'is_purchasable'        => $this->getWcProduct()->is_purchasable(),
            'is_sold_individually'  => $this->getWcProduct()->is_sold_individually() ? 'yes' : 'no',
            'is_virtual'            => $this->getWcProduct()->is_virtual(),
            'max_qty'               => 0 < $this->getWcProduct()->get_max_purchase_quantity()
                ? $this->getWcProduct()->get_max_purchase_quantity() : '',
            'min_qty'               => $this->getWcProduct()->get_min_purchase_quantity(),
            'price_html'            => '<span class="price">' . $this->getWcProduct()->get_price_html() . '</span>',
            'sku'                   => $this->getWcProduct()->get_sku(),
            'description'           => wc_format_content($this->getWcProduct()->get_description()),
            'weight'                => $this->getWcProduct()->get_weight(),
            'weight_html'           => wc_format_weight($this->getWcProduct()->get_weight()),
            'currency'              => get_woocommerce_currency_symbol(),
            'price_with_tax'        => $this->getPriceIncludingTax(),
            'price_without_tax'     => $this->getPriceExcludingTax()
        ], $infos);
    }

    /**
     * @inheritDoc
     */
    public function getMaxPrice(bool $with_tax = true): float
    {
        if ($with_tax) {
            if (!isset($this->max_price['with_tax'])) {
                $this->max_price['with_tax'] = 0;

                if ($this->isVariable()) {
                    $prices = [];
                    foreach ($this->getChildren() as $child) {
                        $prices[] = $child->getPriceIncludingTax();
                    }

                    $this->max_price['with_tax'] = max($prices);
                } else {
                    $this->max_price['with_tax'] = $this->getPriceIncludingTax();
                }
            }
            return $this->max_price['with_tax'];
        } else {
            if (!isset($this->max_price['without_tax'])) {
                $this->max_price['without_tax'] = 0;

                if ($this->isVariable()) {
                    $prices = [];
                    foreach ($this->getChildren() as $child) {
                        $prices[] = $child->getPriceExcludingTax();
                    }

                    $this->max_price['without_tax'] = max($prices);
                } else {
                    $this->max_price['without_tax'] = $this->getPriceExcludingTax();
                }
            }
            return $this->max_price['without_tax'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getMinPrice(bool $with_tax = true): float
    {
        if ($with_tax) {
            if (!isset($this->min_price['with_tax'])) {
                $this->min_price['with_tax'] = 0;

                if ($this->isVariable()) {
                    $prices = [];
                    foreach ($this->getChildren() as $child) {
                        $prices[] = $child->getPriceIncludingTax();
                    }

                    $this->min_price['with_tax'] = min($prices);
                } else {
                    $this->min_price['with_tax'] = $this->getPriceIncludingTax();
                }
            }
            return $this->min_price['with_tax'];
        } else {
            if (!isset($this->min_price['without_tax'])) {
                $this->min_price['without_tax'] = 0;

                if ($this->isVariable()) {
                    $prices = [];
                    foreach ($this->getChildren() as $child) {
                        $prices[] = $child->getPriceExcludingTax();
                    }

                    $this->min_price['without_tax'] = min($prices);
                } else {
                    $this->min_price['without_tax'] = $this->getPriceExcludingTax();
                }
            }
            return $this->min_price['without_tax'];
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductContract|null
     */
    public function getParent(): ?QueryPostContract
    {
        if (is_null($this->parent) && $this->isVariation()) {
            return parent::getParent();
        } else {
            $this->parent = false;
        }

        return $this->parent ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getPriceIncludingTax(array $args = []): float
    {
        return wc_get_price_including_tax($this->getWcProduct(), $args);
    }

    /**
     * @inheritDoc
     */
    public function getPriceExcludingTax(array $args = []): float
    {
        return wc_get_price_excluding_tax($this->getWcProduct(), $args);
    }

    /**
     * {@inheritDoc}
     *
     * @return WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    public function getWcProduct(): WC_Product
    {
        return $this->wc_product;
    }

    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return $this->wc_product->get_sku();
    }

    /**
     * @inheritDoc
     */
    public function hasVariablePrice(): bool
    {
        if ($this->isVariable()) {
            return $this->getMinPrice() < $this->getPriceIncludingTax() ||
                $this->getMaxPrice() > $this->getPriceIncludingTax();
        } elseif ($this->isVariation()) {
            return $this->getPriceIncludingTax() < $this->getParent()->getPriceIncludingTax() ||
                $this->getPriceIncludingTax() > $this->getParent()->getPriceIncludingTax();
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isOnSale(): bool
    {
        return $this->getWcProduct()->is_on_sale();
    }

    /**
     * @inheritDoc
     */
    public function isSimple(): bool
    {
        return ($this->getWcProduct() instanceof WC_Product_Simple) &&
               ! ($this->getWcProduct() instanceof WC_Product_Variation);
    }

    /**
     * @inheritDoc
     */
    public function isVariable(): bool
    {
        return $this->getWcProduct() instanceof WC_Product_Variable;
    }

    /**
     * @inheritDoc
     */
    public function isVariation(): bool
    {
        return $this->getWcProduct() instanceof WC_Product_Variation;
    }
}