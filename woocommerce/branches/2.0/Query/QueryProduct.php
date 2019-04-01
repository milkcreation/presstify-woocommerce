<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\Contracts\QueryProducts as QueryProductsContract;
use tiFy\Plugins\Woocommerce\Contracts\QueryProduct as QueryProductContract;
use tiFy\Support\ParamsBag;
use tiFy\Wordpress\Contracts\QueryPost as QueryPostContract;
use tiFy\Wordpress\Query\QueryPost;
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;
use WP_Post;

/**
 * Class QueryProduct
 * @package tiFy\Plugins\Woocommerce\Query
 *
 * @mixin QueryPost
 */
class QueryProduct extends ParamsBag implements QueryProductContract
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
     * Objet tiFy du produit Parent.
     * {@internal Variation uniquement}
     * @var null|QueryProductContract
     */
    protected $parent;

    /**
     * Objet Post tiFy.
     * @var null|QueryPostContract
     */
    protected $query_post;

    /**
     * Objet Product Woocommerce.
     * @var WC_Product
     */
    protected $wc_product;

    /**
     * Objet Post Wordpress.
     * @var null|WP_Post
     */
    protected $wp_post;

    /**
     * Liste des instances de variations associées au produit.
     * {@internal Le produit doit être de type variable}
     * @var null|array|QueryProductsContract|QueryProductContract[]
     */
    protected $variations;

    /**
     * @inheritdoc
     */
    public static function createFromGlobal(): ?QueryProductContract
    {
        global $product, $post;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product($post);
        }

        return $product instanceof WC_Product ? new static($product) : null;
    }

    /**
     * @inheritdoc
     */
    public static function createFromId($product_id): ?QueryProductContract
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
     * CONSTRUCTEUR.
     *
     * @param WC_Product $wc_product Objet Product Woocommerce.
     *
     * @return void
     */
    public function __construct(WC_Product $wc_product)
    {
        $this->wc_product = $wc_product;

        $this->set($this->wc_product->get_data())->parse();
    }

    /**
     * Appel des méthodes du QueryPost associé.
     *
     * @param string $name Nom de qualification de la methode.
     * @param array $args Liste des variables passées en argument à la méthode.
     *
     * @return null|QueryPostContract
     */
    public function __call($name, $args)
    {
        return method_exists($this->getQueryPost(), $name)
            ? call_user_func_array([$this->getQueryPost(), $name], $args)
            : null;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        if (is_null($this->children)) {
            $this->children = $this->isVariable()
                ? QueryProducts::createFromIds($this->getProduct()->get_children())
                : [];
        }

        return $this->children ? : null;
    }

    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return $this->wc_product->get_id();
    }

    /**
     * @inheritdoc
     */
    public function getMaxPrice($with_tax = true): float
    {
        if ($with_tax) {
            if ( ! isset($this->max_price['with_tax'])) {
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
            if ( ! isset($this->max_price['without_tax'])) {
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
     * @inheritdoc
     */
    public function getMinPrice($with_tax = true): float
    {
        if ($with_tax) {
            if ( ! isset($this->min_price['with_tax'])) {
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
            if ( ! isset($this->min_price['without_tax'])) {
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
     * @inheritdoc
     */
    public function getParent()
    {
        if ($this->isVariation()){
            if (is_null($this->parent)) {
                $this->parent = static::createFromId($this->getProduct()->get_parent_id()) ? : false;
            }
            return $this->parent ? : null;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getPost(): ?WP_Post
    {
        if (is_null($this->wp_post)) {
            $this->wp_post = get_post($this->getId());
        }

        return $this->wp_post instanceof WP_Post ? $this->wp_post : null;
    }

    /**
     * @inheritdoc
     */
    public function getPriceIncludingTax($args = []): float
    {
        return wc_get_price_including_tax($this->getProduct(), $args);
    }

    /**
     * @inheritdoc
     */
    public function getPriceExcludingTax($args = []): float
    {
        return wc_get_price_excluding_tax($this->getProduct(), $args);
    }

    /**
     * @inheritdoc
     */
    public function getProduct(): WC_Product
    {
        return $this->wc_product;
    }

    /**
     * @inheritdoc
     */
    public function getQueryPost(): ?QueryPostContract
    {
        if (is_null($this->query_post)) {
            $this->query_post = new QueryPost($this->getPost());
        }

        return $this->query_post instanceof QueryPostContract ? $this->query_post : null;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function isOnSale(): bool
    {
        return $this->getProduct()->is_on_sale();
    }

    /**
     * @inheritdoc
     */
    public function isSimple(): bool
    {
        return $this->getProduct() instanceof WC_Product_Simple;
    }

    /**
     * @inheritdoc
     */
    public function isVariable(): bool
    {
        return $this->getProduct() instanceof WC_Product_Variable;
    }

    /**
     * @inheritdoc
     */
    public function isVariation(): bool
    {
        return $this->getProduct() instanceof WC_Product_Variation;
    }
}