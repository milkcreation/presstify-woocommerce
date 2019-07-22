<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\TemplateHooks;

use tiFy\Support\ParamsBag;
use tiFy\Plugins\Woocommerce\{Contracts\TemplateHooks as TemplateHooksContract, WoocommerceAwareTrait};
use ReflectionException;
use ReflectionFunction;

/**
 * @see Woocommerce/includes/wc-template-hooks.php
 */
class TemplateHooks extends ParamsBag implements TemplateHooksContract
{
    use WoocommerceAwareTrait;

    /**
     * Liste des éléments d'accroche natifs.
     * @var array
     */
    protected $hooks = [
        // GLOBAL - ARCHIVE PRODUITS & PAGE PRODUIT & CATEGORIE DE PRODUIT
        // Pré-affichage de la page de contenu
        'woocommerce_before_main_content'           => [
            'woocommerce_output_content_wrapper' => 10,
            'woocommerce_breadcrumb'             => 20
        ],
        // Post-affichage de la page de contenu
        'woocommerce_after_main_content'            => [
            'woocommerce_output_content_wrapper_end' => 10
        ],

        // ARCHIVE PRODUITS
        // Description de la page d'archive
        'woocommerce_archive_description'           => [
            'woocommerce_taxonomy_archive_description' => 10,
            'woocommerce_product_archive_description'  => 10
        ],
        // Pré-affichage de la boucle produit
        'woocommerce_before_shop_loop'              => [
            'wc_print_notices'             => 10,
            'woocommerce_result_count'     => 20,
            'woocommerce_catalog_ordering' => 30
        ],
        // Pré-affichage d'un élément de la boucle
        'woocommerce_before_shop_loop_item'         => [
            'woocommerce_template_loop_product_link_open' => 10
        ],
        // Avant l'affichage du titre d'un élément de la boucle
        'woocommerce_before_shop_loop_item_title'   => [
            'woocommerce_show_product_loop_sale_flash'    => 10,
            'woocommerce_template_loop_product_thumbnail' => 10
        ],
        // Affichage du titre d'un élément de la boucle
        'woocommerce_shop_loop_item_title'          => [
            'woocommerce_template_loop_product_title' => 10
        ],
        // Après l'affichage du titre d'un élément de la boucle
        'woocommerce_after_shop_loop_item_title'    => [
            'woocommerce_template_loop_rating' => 5,
            'woocommerce_template_loop_price'  => 10
        ],
        // Post-affichage d'un élément de la boucle
        'woocommerce_after_shop_loop_item'          => [
            'woocommerce_template_loop_product_link_close' => 5,
            'woocommerce_template_loop_add_to_cart'        => 10
        ],
        // Post-affichage de la boucle produit
        'woocommerce_after_shop_loop'               => [
            'woocommerce_pagination' => 10
        ],
        // Aucun produit trouvé
        'woocommerce_no_products_found'             => [
            'wc_no_products_found' => 10
        ],

        // PAGE PRODUIT
        // Pré-affichage du produit
        'woocommerce_before_single_product'         => [
            'wc_print_notices' => 10
        ],
        // Pré-affichage de la fiche produit
        'woocommerce_before_single_product_summary' => [
            'woocommerce_show_product_sale_flash' => 10,
            'woocommerce_show_product_images'     => 20
        ],
        // Fiche produit
        'woocommerce_single_product_summary'        => [
            'woocommerce_template_single_title'       => 5,
            'woocommerce_template_single_rating'      => 10,
            'woocommerce_template_single_price'       => 10,
            'woocommerce_template_single_excerpt'     => 20,
            'woocommerce_template_single_add_to_cart' => 30,
            'woocommerce_template_single_meta'        => 40,
            'woocommerce_template_single_sharing'     => 50
        ],
        // Post-affichage de la fiche produit
        'woocommerce_after_single_product_summary'  => [
            'woocommerce_output_product_data_tabs' => 10,
            'woocommerce_upsell_display'           => 15,
            'woocommerce_output_related_products'  => 20
        ],
        // Post-affichage du produit
        'woocommerce_after_single_product'          => [],

        // CATEGORIE DE PRODUIT
        // Pré-affichage d'une catégorie
        'woocommerce_before_subcategory'            => [
            'woocommerce_template_loop_category_link_open' => 10
        ],
        // Affichage avant le titre
        'woocommerce_before_subcategory_title'      => [
            'woocommerce_subcategory_thumbnail' => 10
        ],
        // Affichage du titre
        'woocommerce_shop_loop_subcategory_title'   => [
            'woocommerce_template_loop_category_title' => 10
        ],
        // Affichage après le titre
        'woocommerce_after_subcategory_title'       => [],
        // Post-affichage d'une catégorie
        'woocommerce_after_subcategory'             => [
            'woocommerce_template_loop_category_link_close' => 10
        ],

        // BARRE LATERAL
        'woocommerce_sidebar'                       => [
            'woocommerce_get_sidebar' => 10
        ],
        // Panier
        'woocommerce_cart_collaterals'              => [
            'woocommerce_cross_sell_display' => 10,
            'woocommerce_cart_totals'        => 10
        ]
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        // Traitement des crochets définis dans la configuration.
        add_action('wp', function () {
            foreach ($this->all() as $tag => $functions) {
                if (!isset($this->hooks[$tag])) {
                    $this->hooks[$tag] = [];
                }

                foreach ($functions as $function => $priority) {
                    if (!isset($this->hooks[$tag][$function])) {
                        $this->hookAdd($tag, $function, $priority);
                    } else {
                        if (is_null($priority) || $priority === false) {
                            $this->hookRemove($tag, $function, $this->hooks[$tag][$function]);
                        } elseif ($this->hooks[$tag][$function] !== $priority) {
                            $this->hookChange($tag, $function, $this->hooks[$tag][$function], $priority);
                        }
                    }
                }
            }
        });

        add_action('wp', function () {
            if ($matches = preg_grep('/^woocommerce_/', get_class_methods($this))) {
                foreach ($matches as $tag) {
                    if (!isset($this->hooks[$tag])) {
                        continue;
                    }
                    add_action($tag, [$this, $tag], -99);
                }
            }
        }, 99);

        $this->boot();
    }

    /**
     * Récupèration du nom de qualification d'une fonction.
     * {@internal Pour les fonctions anonymes retourne une chaîne sérialisée.}
     *
     * @param callable $function
     *
     * @return string|null
     */
    private function _getFunctionName($function): ?string
    {
        if (is_string($function)) {
            return $function;
        } else {
            try {
                $rf = new ReflectionFunction($function);
                return (string)$rf;
            } catch (ReflectionException $exception) {
                return null;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function hookAdd(string $tag, callable $function, $priority = 10): bool
    {
        if ($function_id = $this->_getFunctionName($function)) {
            $this->hooks[$tag][$function_id] = $priority;

            return add_action($tag, $function, $priority);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hookChange($tag, $function, $old = 10, $new = 10): bool
    {
        $this->hookRemove($tag, $function, $old);

        return $this->hookAdd($tag, $function, $new);
    }

    /**
     * @inheritDoc
     */
    public function hookRemove(string $tag, callable $function, ?int $priority = null): bool
    {
        if ($function_id = $this->_getFunctionName($function)) {
            if ($this->hooks[$tag]) {
                unset($this->hooks[$tag][$function]);
            }
            return remove_action($tag, $function, $priority);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function parse(): TemplateHooksContract
    {
        parent::parse();

        foreach($this->all() as $tag => $functions) {
            $default = $this->hooks[$tag] ?? [];
            $this->set($tag, is_null($functions) ? [] : array_merge($default, $functions));
        }

        return $this;
    }

    /**
     * Exemple de contextualisation.
     *
     * @return void
     */
    public function woocommerce_before_main_content(): void
    {
        $this->hookAdd(__FUNCTION__, '__return_false', 1);
    }
}   