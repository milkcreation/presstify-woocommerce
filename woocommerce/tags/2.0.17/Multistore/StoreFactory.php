<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Multistore;

use tiFy\Metabox\MetaboxManager;
use tiFy\Plugins\Woocommerce\Contracts\{Multistore as MultistoreContract, StoreFactory as StoreFactoryContract};
use tiFy\Support\ParamsBag;
use tiFy\Wordpress\Contracts\{QueryPost as QueryPostContract, QueryTerm as QueryTermContract};
use tiFy\Wordpress\Query\{QueryPost, QueryTerm};

class StoreFactory extends ParamsBag implements StoreFactoryContract
{
    /**
     * Indicateur d'initialisation.
     * @var boolean
     */
    protected $booted = false;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de métaboxe associées.
     * @var array
     */
    protected $optionsMetaboxes = [];

    /**
     * Instance du gestionnaire de magasins.
     * @var MultistoreContract
     */
    protected $stores;

    /**
     * Initialisation du magasin.
     *
     * @return void
     */
    public function boot(): void
    {

    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'title' => $this->getName()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDisplayMode(string $default = 'products'): string
    {
        return (string)get_option('tify_wc_'. $this->getName() .'_page_display', $default);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPage(): ?QueryPostContract
    {
        return ($id = $this->getPageId()) ? QueryPost::createFromId($id) : null;
    }

    /**
     * @inheritDoc
     */
    public function getPageId(): int
    {
        return (int)get_option('tify_wc_' . $this->getName() . '_hook', 0);
    }

    /**
     * @inheritDoc
     */
    public function getProductCat(): ?QueryTermContract
    {
        return ($id = $this->getProductCatId()) ? QueryTerm::createFromId($id) : null;
    }

    /**
     * @inheritDoc
     */
    public function getProductCatId(): int
    {
        return (int)get_option('tify_wc_' . $this->getName() . '_cat', 0);
    }

    /**
     * @inheritDoc
     */
    public function isCurrent(): bool
    {
        if (is_singular() && (get_the_ID() == $this->getPageId())) {
            // > Page d'accroche associée.
            return true;
        } elseif ($term_id = $this->getProductCatId()) {
            if (is_tax('product_cat', $term_id)) {
                // > Page liste de la catégorie de produit associée.
                return true;
            } elseif (
                is_tax('product_cat') &&
                term_is_ancestor_of($term_id, get_queried_object()->term_id, 'product_cat')
            ) {
                // > Page liste d'une catégorie enfant de la catégorie de produit associée.
                return true;
            } elseif (is_singular('product')) {
                // Page d'un produit de la catégorie de produit associé.
                $terms = wp_get_post_terms(get_the_ID(), 'product_cat', ['fields' => 'ids']);
                if (is_wp_error($terms)) {
                    return false;
                }

                foreach ($terms as $term) {
                    if (term_is_ancestor_of($term_id, $term, 'product_cat')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $name, MultistoreContract $stores): StoreFactoryContract
    {
        if (!$this->booted) {
            $this->name = $name;
            $this->stores = $stores;

            $this->setOptionsMetabox('WoocommerceMultistore-storeOptions--' . $this->name . '_general', [
                'title'   => __('Options générales', 'tify'),
                'position' => 1,
                'content' => StoreOptionsMetabox::class
            ]);

            $this->boot();
            $this->booted = true;

            add_action('init', function () {
                $this->setOptionsMetabox('WoocommerceMultistore-storeOptions--' . $this->name, [
                    'parent' => 'WoocommerceMultistore-storeOptions',
                    'title'  => $this->get('title')
                ]);

                /** @var MetaboxManager $metabox */
                $metabox = $this->stores->manager()->getContainer()->get('metabox');

                foreach ($this->optionsMetaboxes as $name => $attrs) {
                    $metabox->add($name, 'tify_options@options', $attrs);
                }
            });

            add_filter('pre_option_woocommerce_shop_page_display', function ($value) {
                return $this->isCurrent() ? $this->getDisplayMode() : $value;
            });

            add_filter('woocommerce_product_subcategories_args', function ($args) {
                if ($this->isCurrent() && in_array($this->getDisplayMode(), ['both', 'subcategories'])) {
                    $args['parent'] = $this->getProductCatId();
                }
                return $args;
            });
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOptionsMetabox(string $name, array $attrs = []): StoreFactoryContract
    {
        $attrs = array_merge([
            'parent'  => 'WoocommerceMultistore-storeOptions--' . $this->name,
            'args'    => []
        ], $attrs);
        $attrs['args']['store'] = $this;
        $this->optionsMetaboxes[$name] = $attrs;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stores(): MultistoreContract
    {
        return $this->stores;
    }
}