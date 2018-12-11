<?php

namespace tiFy\Plugins\Woocommerce\Views;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\TemplateHooks as TemplateHooksContract;

/**
 * ACCROCHAGE / DECROCHAGE / RE-ORDONNANCEMENT DES ELEMENTS DE TEMPLATES
 * @see Woocommerce/includes/wc-template-hooks.php
 */
class TemplateHooks extends ParamsBag implements TemplateHooksContract
{
    /**
     * Liste des accroches d'éléments de template.
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
     * @param array $hooks Listes des accroches d'éléments de template.
     *
     * @return void
     */
    public function __construct($hooks = [])
    {
        parent::__construct($hooks);

        $this->process($this->all());
        $this->processDeferred();
    }

    /**
     * Accrochage d'un élément de template.
     *
     * @param string $tag Identifiant du crochet.
     * @param string $function Fonction attachée.
     * @param int $priority Priorité d'exécution de la fonction attachée.
     *
     * @throws \ReflectionException
     *
     * @return bool|null
     */
    public function add($tag, $function, $priority = 10)
    {
        // Bypass
        if (!isset($this->hooks[$tag])) :
            return null;
        endif;

        $function_id = $this->getFunctionIdentifier($function);

        $this->hooks[$tag][$function_id] = $priority;

        return add_action($tag, $function, $priority);
    }

    /**
     * Ré-accrochage d'un élément de template.
     *
     * @param string $tag Identifiant du crochet.
     * @param string $function Fonction à ré-attacher.
     * @param int $priority Priorité d'exécution de la fonction attachée.
     *
     * @throws \ReflectionException
     *
     * @return null
     */
    public function change($tag, $function, $priority = 10)
    {
        // Bypass
        if (!isset($this->hooks[$tag]) || !isset($this->hooks[$tag][$function])) :
            return null;
        endif;

        if ($this->remove($tag, $function, $this->hooks[$tag][$function])) :
            $this->add($tag, $function, $priority);
        endif;

        return null;
    }

    /**
     * Récupère l'identifiant d'une fonction, une fonction anonyme sera sérialisée.
     *
     * @param string $func
     *
     * @throws \ReflectionException
     *
     * @return string|null
     */
    protected function getFunctionIdentifier($func)
    {
        if (is_string($func)) :
            return $func;
        endif;

        try {
            $rf = new \ReflectionFunction($func);
        } catch (\ReflectionException $exception) {
            return null;
        }

        return $rf->__toString();
    }

    /**
     * Traitement des crochets définis dans la configuration.
     *
     * @param array $hooks Crochets.
     *
     * @return void
     */
    public function process($hooks)
    {
        add_action(
            'init',
            function () use ($hooks) {
                foreach ((array)$hooks as $tag => $functions) :
                    if (!isset($this->hooks[$tag])) :
                        $this->registerHook($tag);
                    endif;

                    if (empty($functions)) :
                        continue;
                    endif;

                    foreach ($functions as $function => $priority) :
                        if (!isset($this->hooks[$tag][$function])) :
                            $this->add($tag, $function, $priority);
                        elseif (!$priority) :
                            $this->remove($tag, $function, $this->hooks[$tag][$function]);
                        elseif ($this->hooks[$tag][$function] !== (int)$priority) :
                            $this->change($tag, $function, $priority);
                        endif;
                    endforeach;
                endforeach;
            }
        );
    }

    /**
     * Traitement différé des crochets.
     *
     * @return void
     */
    public function processDeferred()
    {
        add_action(
            'wp',
            function () {
                if ($matches = preg_grep('/^woocommerce_/', get_class_methods($this))) :
                    foreach ($matches as $tag) :
                        if (!isset($this->hooks[$tag])) :
                            continue;
                        endif;

                        add_action($tag, [$this, $tag], -99);
                    endforeach;
                endif;
            },
            99
        );
    }

    /**
     * Déclaration d'un emplacement d'accroche personnalisé.
     *
     * @return void
     */
    public function registerHook($tag)
    {
        if (!isset($this->hooks[$tag])) :
            $this->hooks[$tag] = [];
        endif;
    }

    /**
     * Décrochage d'un élément de template.
     *
     * @param string $tag Identifiant du crochet.
     * @param string $function Fonction à détacher.
     * @param int $priority Priorité d'exécution de la fonction attachée.
     *
     * @return bool|null
     */
    public function remove($tag, $function, $priority = 10)
    {
        // Bypass
        if (!isset($this->hooks[$tag])) :
            return null;
        endif;

        if ($rm = remove_action($tag, $function, $priority)) :
            unset($this->hooks[$tag][$function]);
        endif;

        return $rm;
    }

    /**
     * SURCHAGE
     */
    /**
     * Exemple de Contextualisation.
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    public function woocommerce_before_main_content()
    {
        $this->add(__FUNCTION__, '__return_false', 1);
    }
}   