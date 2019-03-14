<?php

namespace tiFy\Plugins\Woocommerce\Multishop;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\MultishopFactory as MultishopFactoryContract;

class Factory extends ParamsBag implements MultishopFactoryContract
{
    /**
     * Identifiant
     */
    protected $ShopId = null;

    /**
     * Paramètres
     */
    protected $Attrs = [];

    /**
     * CONSTRUCTEUR
     *
     * @param int $id
     * @param array $attrs
     */
    public function __construct($id, $attrs = [])
    {
        parent::__construct($attrs);

        $this->ShopId = $id;
        $this->Attrs = wp_parse_args(
            $attrs,
            [
                'title' => $id
            ]
        );

        // Déclaration des sections de boîtes à onglets
        add_action('tify_options_register_node', [$this, '_tify_options_register_node']);

        // Déclaration des sections de boîtes à onglets
        add_filter('body_class', [$this, '_body_class']);
    }

    /**
     * Déclaration des onglets d'édition de la boutique
     */
    final public function _tify_options_register_node()
    {
        tify_options_register_node(
            [
                'id'     => 'tiFyPluginsMultiShop-' . $this->getID(),
                'parent' => 'tiFyPluginsMultiShop-Options',
                'title'  => $this->Attrs['title']
            ]
        );
        tify_options_register_node(
            [
                'id'     => 'tiFyPluginsMultiShop-' . $this->getID() . '--options',
                'parent' => 'tiFyPluginsMultiShop-' . $this->getID(),
                'title'  => __('Options générales', 'tify'),
                'order'  => 1,
                'cb'     => '\tiFy\Plugins\Woocommerce\MultiShop\Taboox\Option\MultiShopGeneralOptions\Admin\MultiShopGeneralOptions',
                'args'   => [
                    'shop_id' => $this->getID()
                ]
            ]
        );
    }

    /**
     *
     */
    final public function _body_class($class)
    {
        if ($this->isIn()) :
            $class[] = 'tiFyWC-Shop--' . $this->getID();
        endif;

        return $class;
    }

    /**
     * Récupération de l'identifiant
     */
    final public function getID()
    {
        return $this->ShopId;
    }

    /**
     * Récupération de la page d'accroche
     */
    final public function getHookId()
    {
        return (int)get_option('tify_wc_' . $this->getID() . '_hook', 0);
    }

    /**
     * Récupération de l'identifiant catégorie d'affichage
     */
    final public function getTermId()
    {
        return (int)get_option('tify_wc_' . $this->getID() . '_cat', 0);
    }

    /**
     * Récupération de la catégorie d'affichage
     */
    final public function getTerm()
    {
        if (($term_id = $this->getTermId()) && ($term = get_term($term_id, 'product_cat')) && !is_wp_error($term)) {
            return $term;
        }
    }

    /**
     * Vérifie si la page courante fait partie de l'ecosystème de cette boutique
     */
    final public function isIn()
    {
        // Page d'accroche
        if (is_singular() && (get_the_ID() == $this->getHookId())) {
            return true;
        }

        if ($term_id = $this->getTermId()) :
            // Page liste des catégories
            if (is_tax('product_cat', $term_id)) {
                return true;
            }
            if (is_tax('product_cat') && term_is_ancestor_of($term_id, get_queried_object()->term_id, 'product_cat')) {
                return true;
            }

            // Page produit
            if (is_singular('product')):
                $terms = wp_get_post_terms(get_the_ID(), 'product_cat', ['fields' => 'ids']);
                if (is_wp_error($terms)) {
                    return false;
                }

                foreach ($terms as $term) :
                    if (term_is_ancestor_of($term_id, $term, 'product_cat')) :
                        return true;
                    endif;
                endforeach;
            endif;
        endif;

        return false;
    }
}