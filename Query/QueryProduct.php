<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Contracts\Support\{Collection as CollectionContract, ParamsBag as ParamsBagContract};
use tiFy\Plugins\Woocommerce\Contracts\{QueryProduct as QueryProductContract, QueryProducts as QueryProductsContract};
use tiFy\Support\{Collection, ParamsBag};
use tiFy\Wordpress\{Contracts\QueryPost as QueryPostContract, Query\QueryPost};
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;

class QueryProduct extends QueryPost implements QueryProductContract
{
    /**
     * Liste des informations associées à un produit.
     * @var ParamsBagContract|null
     */
    protected $infos;

    /**
     * Prix de vente maximum (avec et sans taxe).
     * @var array|null
     */
    protected $maxPrice;

    /**
     * Prix de vente minimum (avec et sans taxe).
     * @var array|null
     */
    protected $minPrice;

    /**
     * Instance du produit parent.
     * {@internal Variation uniquement}
     * @var QueryProductContract|false|null
     */
    protected $parent;

    /**
     * Liste des instances de toutes les variations d'un produit variable.
     * {@internal Le produit doit être de type variable}
     * @var QueryProductsContract|QueryProductContract[]|array|null
     */
    protected $variations;

    /**
     * Liste des instances de variations disponibles d'un produit variable.
     * {@internal Produit variable uniquement}
     * @var QueryProductsContract|QueryProductsContract[]|array|null
     */
    protected $variationsAvailable;

    /**
     * Objet Product Woocommerce.
     * @var WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation|null
     */
    protected $wcProduct;

    /**
     * CONSTRUCTEUR.
     *
     * @param WC_Product|null $wc_product Objet Product Woocommerce.
     *
     * @return void
     */
    public function __construct(?WC_Product $wc_product = null)
    {
        if ($this->wcProduct = $wc_product instanceof WC_Product ? $wc_product : null) {
            parent::__construct(get_post($this->wcProduct->get_id()));
        }
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
    public function getDatas(): array
    {
        $datas = [];

        $datas['id'] = $this->getId();

        if ($this->isVariable()) {
            $datas['type'] = 'variable';
            $datas['infos'] = [];
            foreach ($this->getVariations() as $var) {
                $datas['infos'][] = $var->getInfos()->all();
            }
        } elseif ($this->isSimple()) {
            $datas['type'] = 'simple';
            $datas['infos'] = $this->getInfos()->all();
        }

        return $datas;
    }

    /**
     * @inheritDoc
     */
    public function getInfos(?string $key = null, $default = null)
    {
        if (!$this->infos instanceof ParamsBagContract) {
            if (!$this->cacheHas('infos')) {
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

                $infos = (new ParamsBag())->set(array_merge([
                    'availability_html'     => wc_get_stock_html($this->getWcProduct()),
                    'backorders_allowed'    => $this->getWcProduct()->backorders_allowed(),
                    'currency'              => get_woocommerce_currency_symbol(),
                    'description'           => wc_format_content($this->getWcProduct()->get_description()),
                    'dimensions'            => $this->getWcProduct()->get_dimensions(false),
                    'dimensions_html'       => wc_format_dimensions($this->getWcProduct()->get_dimensions(false)),
                    'display_price'         => wc_get_price_to_display($this->getWcProduct()),
                    'display_regular_price' => wc_get_price_to_display($this->getWcProduct(), [
                        'price' => $this->getWcProduct()->get_regular_price(),
                    ]),
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
                    'price_html'            => '<span class="price">' .
                        $this->getWcProduct()->get_price_html() .
                        '</span>',
                    'price_with_tax'        => $this->getPriceIncludingTax(),
                    'price_without_tax'     => $this->getPriceExcludingTax(),
                    'sku'                   => $this->getWcProduct()->get_sku(),
                    'weight'                => $this->getWcProduct()->get_weight(),
                    'weight_html'           => wc_format_weight($this->getWcProduct()->get_weight()),
                ], $infos));

                $this->cacheAdd('infos', $this->infos = $infos);
            } else {
                $this->infos = $this->cacheGet('infos', []);
            }
        }

        return is_null($key) ? $this->infos : $this->infos->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMaxPrice(?bool $with_tax = null): float
    {
        if (is_null($with_tax)) {
            $with_tax = !!get_option('woocommerce_tax_display_shop');
        }

        if ($with_tax) {
            if (!isset($this->maxPrice['with_tax'])) {
                if (!$this->cacheHas('max_price_with_tax')) {
                    if ($this->isVariable()) {
                        $prices = [];
                        foreach ($this->getVariationsAvailable() as $var) {
                            $prices[] = $var->getPriceIncludingTax();
                        }

                        $price = max($prices);
                    } else {
                        $price = $this->getPriceIncludingTax();
                    }
                    $this->cacheAdd('max_price_with_tax', $price);
                } else {
                    $price = $this->cacheGet('max_price_with_tax', 0);
                }
                $this->maxPrice['with_tax'] = (float)$price;
            }
            return $this->maxPrice['with_tax'];
        } else {
            if (!isset($this->maxPrice['without_tax'])) {
                if (!$this->cacheHas('max_price_without_tax')) {
                    if ($this->isVariable()) {
                        $prices = [];
                        foreach ($this->getVariationsAvailable() as $var) {
                            $prices[] = $var->getPriceExcludingTax();
                        }

                        $price = max($prices);
                    } else {
                        $price = $this->getPriceExcludingTax();
                    }
                    $this->cacheAdd('max_price_without_tax', $price);
                } else {
                    $price = $this->cacheGet('max_price_without_tax', 0);
                }
                $this->maxPrice['without_tax'] = (float)$price;
            }
            return $this->maxPrice['without_tax'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getMinPrice(?bool $with_tax = null): float
    {
        if (is_null($with_tax)) {
            $with_tax = !!get_option('woocommerce_tax_display_shop');
        }

        if ($with_tax) {
            if (!isset($this->minPrice['with_tax'])) {
                if (!$this->cacheHas('min_price_with_tax')) {
                    if ($this->isVariable()) {
                        $prices = [];
                        foreach ($this->getVariationsAvailable() as $var) {
                            $prices[] = $var->getPriceIncludingTax();
                        }

                        $price = min($prices);
                    } else {
                        $price = $this->getPriceIncludingTax();
                    }
                    $this->cacheAdd('min_price_with_tax', $price);
                } else {
                    $price = $this->cacheGet('min_price_with_tax', 0);
                }
                $this->minPrice['with_tax'] = (float)$price;
            }
            return $this->minPrice['with_tax'];
        } else {
            if (!isset($this->minPrice['without_tax'])) {
                if (!$this->cacheHas('min_price_without_tax')) {
                    if ($this->isVariable()) {
                        $prices = [];
                        foreach ($this->getVariationsAvailable() as $var) {
                            $prices[] = $var->getPriceExcludingTax();
                        }

                        $price = min($prices);
                    } else {
                        $price = $this->getPriceExcludingTax();
                    }
                    $this->cacheAdd('min_price_without_tax', $price);
                } else {
                    $price = $this->cacheGet('min_price_without_tax');
                }
                $this->minPrice['without_tax'] = (float)$price;
            }
            return $this->minPrice['without_tax'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getPriceIncludingTax(array $args = []): float
    {
        return (float)wc_get_price_including_tax($this->getWcProduct(), $args);
    }

    /**
     * @inheritDoc
     */
    public function getPriceExcludingTax(array $args = []): float
    {
        return (float)wc_get_price_excluding_tax($this->getWcProduct(), $args);
    }

    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return $this->wcProduct->get_sku();
    }

    /**
     * @inheritDoc
     */
    public function getVariationPrice($min = true): float
    {
        if (($vPrices = $this->getVariationPrices()) && ($prices = $vPrices->get('price'))) {
            return (float)($min ? current($prices) : end($prices));
        }

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getVariationPrices(): ?CollectionContract
    {
        $prices = [];
        if ($this->isVariable()) {
            if (!$this->cacheHas('variation_prices')) {
                $this->cacheAdd('variation_prices', $prices = $this->getWcProduct()->get_variation_prices());
            } else {
                $prices = $this->cacheGet('variation_prices', []);
            }
        }
        return $prices ? Collection::createFromItems($prices) : null;
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductsContract|QueryProductContract[]|null
     */
    public function getVariations(): ?QueryProductsContract
    {
        if (is_null($this->variations)) {
            $this->variations = [];
            if ($this->isVariable() && ($ids = $this->getWcProduct()->get_children())) {
                $vars = QueryProducts::createFromArgs([
                    'post__in'       => $ids,
                    'post_type'      => 'product_variation',
                    'post_status'    => ['publish', 'private'],
                    'posts_per_page' => -1,
                ]);
                $this->variations = $vars->count() ? $vars : [];
            }
        }

        return $this->variations ?: null;
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductsContract|QueryProductContract[]|null
     */
    public function getVariationsAvailable(): ?QueryProductsContract
    {
        if (is_null($this->variationsAvailable)) {
            if ($this->isVariable()) {
                if (!$vars = $this->getVariations()) {
                    $this->variationsAvailable = [];
                } else {
                    $exists = $vars->collect()->filter(function (QueryProductContract $var) {
                        $prod = $var->getWcProduct();

                        if (
                            !$prod || !$prod->exists() ||
                            ('yes' === get_option('woocommerce_hide_out_of_stock_items') && !$prod->is_in_stock())
                        ) {
                            return false;
                        } elseif (
                            apply_filters('woocommerce_hide_invisible_variations', true, $this->getId(), $prod) &&
                            !$prod->variation_is_visible()
                        ) {
                            return false;
                        }
                        return true;
                    });

                    $this->variationsAvailable = !!$exists->count()
                        ? QueryProducts::createFromItems($exists->all()) : [];
                }
            } else {
                $this->variationsAvailable = [];
            }
        }

        return $this->variationsAvailable ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getVariationsAvailableInfos(): ?CollectionContract
    {
        $aVars = [];
        if ($this->isVariable()) {
            foreach ($this->getVariationsAvailable() as $var) {
                $aVars[] = $var->getInfos();
            }

            $aVars = array_values(array_filter($aVars));
        }

        return $aVars ? Collection::createFromItems($aVars) : null;
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProducts|QueryProduct[]|null
     */
    public function getVariationsVisible(): ?QueryProductsContract
    {
        $visible = [];
        if ($vars = $this->getVariations()) {
            $exists = $vars->collect()->filter(function (QueryProductContract $var) {
                return $var->getInfos('variation_is_visible', false);
            });
            $visible = !!$exists->count() ? QueryProducts::createFromItems($exists->all()) : [];
        }

        return $visible ?: null;
    }

    /**
     * {@inheritDoc}
     *
     * @return WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation
     */
    public function getWcProduct(): WC_Product
    {
        return $this->wcProduct;
    }

    /**
     * @inheritDoc
     */
    public function hasVariation(): bool
    {
        if ($this->isVariable() && ($vars = $this->getVariations())) {
            return $vars->count() > 1;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasVariablePrice(): bool
    {
        if ($this->isVariable()) {
            return $this->getMinPrice(true) < $this->getPriceIncludingTax() ||
                $this->getMaxPrice(true) > $this->getPriceIncludingTax();
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
            !($this->getWcProduct() instanceof WC_Product_Variation);
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