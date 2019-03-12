<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use tiFy\Plugins\Woocommerce\Contracts\QueryProducts as QueryProductsContract;
use tiFy\Plugins\Woocommerce\Contracts\QueryProduct as QueryProductContract;
use tiFy\Support\ParamsBag;
use tiFy\Contracts\Wp\QueryPost as QueryPostContract;
use tiFy\Wp\Query\QueryPost;
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
     * Objet tiFy du produit Parent.
     * {@internal WC_Product_Variation uniquement}
     * @var null|QueryProductContract
     */
    protected $product_parent;

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
     * CONSTRUCTEUR.
     *
     * @param WC_Product $wc_product Objet Product Woocommerce.
     *
     * @return void
     */
    public function __construct(WC_Product $wc_product)
    {
        $this->wc_product = $wc_product;

        $this->setAttrs($this->wc_product->get_data());
    }

    /**
     * Appel des méthodes du QueryPost associé.
     *
     * @param string $name Nom de qualification de la methode.
     * @param array $args Liste des variables passées en argument à la méthode.
     *
     * @return mixed
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
     * @inheritdoc
     */
    public function getId(): int
    {
        return $this->wc_product->get_id();
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
    public function getProductParent(): ?QueryProductContract
    {
        if ($this->isVariation()){
            if (is_null($this->product_parent)) {
                $this->product_parent = static::createFromId($this->getProduct()->get_parent_id()) ? : false;
            }
            return $this->product_parent ? : null;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getQueryPost(): ?QueryPostContract
    {
        if (is_null($this->query_post)) :
            $this->query_post = new QueryPost($this->getPost());
        endif;

        return $this->query_post instanceof QueryPostContract ? $this->query_post : null;
    }

    /**
     * @inheritdoc
     */
    public function getPost(): ?WP_Post
    {
        if (is_null($this->wp_post)) :
            $this->wp_post = get_post($this->getId());
        endif;

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
    public function getVariations()
    {
        if (is_null($this->variations)) :
            $this->variations = $this->isVariable()
                ? QueryProducts::createFromIds($this->getProduct()->get_children())
                : [];
        endif;

        return $this->variations;
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