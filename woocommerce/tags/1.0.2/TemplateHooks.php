<?php
/**
 * @Overrideable
 */

namespace tiFy\Plugins\Woocommerce;

/**
 * ACCROCHAGE / DECROCHAGE / RE-ORDONNANCEMENT DES ELEMENTS DE TEMPLATES
 * @see woocommerce/includes/wc-template-hooks.php
 */
class TemplateHooks extends \tiFy\App\Factory
{
    /**
     * Listes des accroches d'éléments de template
     */
    protected static $Hooks = [
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
     * CONSTRUCTEUR
     */
    public function __construct($hooks = [])
    {
        parent::__construct();

        foreach ((array)$hooks as $tag => $functions) :
            if (!isset(self::$Hooks[$tag])) :
                $this->registerHook($tag);
            endif;

            if (empty($functions)) :
                continue;
            endif;

            foreach ($functions as $function => $priority) :
                if (!isset(self::$Hooks[$tag][$function])) :
                    $this->add($tag, $function, $priority);
                elseif (!$priority) :
                    $this->remove($tag, $function, self::$Hooks[$tag][$function]);
                elseif (self::$Hooks[$tag][$function] !== (int)$priority) :
                    $this->change($tag, $function, $priority);
                endif;
            endforeach;
        endforeach;

        // Contextualisation
        add_action('wp', [$this, 'wp'], 99);
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration d'un emplacement d'accroche personnalisé
     */
    public function registerHook($tag)
    {
        if (!isset(self::$Hooks[$tag])) :
            self::$Hooks[$tag] = [];
        endif;
    }

    /**
     * Au traitement de la requête principale de wordpress
     */
    public function wp()
    {
        // Contextualisation
        if ($matches = preg_grep('/^woocommerce_/', get_class_methods($this))) :
            foreach ($matches as $tag) :
                if (!isset(self::$Hooks[$tag])) :
                    continue;
                endif;

                add_action($tag, [$this, $tag], -99);
            endforeach;
        endif;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Accrochage d'un élément de template
     */
    final public static function add($tag, $function, $priority = 10)
    {
        // Bypass
        if (!isset(self::$Hooks[$tag])) :
            return;
        endif;

        $function_id = self::getFunctionIdentifier($function);

        self::$Hooks[$tag][$function_id] = $priority;

        return add_action($tag, $function, $priority);
    }

    /**
     * Décrochage d'un élément de template
     */
    final public static function remove($tag, $function, $priority = 10)
    {
        // Bypass
        if (!isset(self::$Hooks[$tag])) :
            return;
        endif;

        if ($rm = remove_action($tag, $function, $priority)) :
            unset(self::$Hooks[$tag][$function]);
        endif;

        return $rm;
    }

    /**
     * Ré-accrochage d'un élément de template
     */
    final public static function change($tag, $function, $priority = 10)
    {
        // Bypass
        if (!isset(self::$Hooks[$tag])) :
            return;
        endif;
        if (!isset(self::$Hooks[$tag][$function])) :
            return;
        endif;

        if (self::remove($tag, $function, self::$Hooks[$tag][$function])) :
            self::add($tag, $function, $priority);
        endif;
    }

    /**
     * Récupère l'identifiant d'une fonction, une fonction anonyme sera sérialisée
     */
    protected static function getFunctionIdentifier($func)
    {
        if (is_string($func)) :
            return $func;
        endif;

        $rf = new \ReflectionFunction($func);

        return $rf->__toString();
    }

    /**
     * SURCHAGE
     */
    /**
     * Exemple de Contextualisation
     */
    public function woocommerce_before_main_content()
    {
        self::add(__FUNCTION__, '__return_false', 1);
    }
}   