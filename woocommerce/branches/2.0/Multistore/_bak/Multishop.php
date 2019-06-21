<?php

namespace tiFy\Plugins\Woocommerce\Multishop;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Multishop as MultishopContract;

class Multishop extends ParamsBag implements MultishopContract
{
    /**
     * Boutiques
     */
    protected static $Shops = [];

    /**
     * CONSTRUCTEUR
     *
     * @param array $shop
     *
     */
    public function __construct()
    {
        // Déclaration des accroches d'archives
        Components::register('HookArchive');

        // Déclenchement des événements
        $this->appAddAction('tify_options_register_node');
        $this->appAddAction('tify_hookarchive_register');
        $this->appAddAction('woocommerce_get_shop_page_id');
    }

    /**
     * Déclaration de HookArchive
     */
    public function tify_hookarchive_register()
    {
        $defaults = [
            'obj'     => 'taxonomy',
            'archive' => 'product_cat',
            'options' => [
                'edit'      => false,
                'post_type' => 'page',
                'permalink' => false,
                'duplicate' => false,
                'rewrite'   => false
            ]
        ];

        // Définition des contenus d'accroche
        $hookarchive = [];
        $hookarchive['product_cat'] = $defaults;
        foreach (self::getShops() as $shop_id => $factory) :

            if (!$hook_id = $factory->getHookId()) {
                continue;
            }

            $hook = [
                'id'        => $hook_id,
                'post_type' => 'page',
                'permalink' => false,
                'edit'      => false
            ];

            if (!$term_id = $factory->getTermId()) :
                $hookarchive[] = wp_parse_args([
                    'obj'     => 'post_type',
                    'archive' => 'product',
                    'hooks'   => [$hook]
                ], $defaults);
                continue;
            endif;

            $hook['term'] = $term_id;
            $hookarchive['product_cat']['hooks'][] = $hook;
        endforeach;

        foreach ($hookarchive as $attrs) :
            HookArchive::register($attrs);
        endforeach;
    }

    /**
     * Court-circuitage de la récupération de l'identifiant de la boutique Woocommerce
     */
    final public function woocommerce_get_shop_page_id()
    {
        $shop_id = get_option('woocommerce_shop_page_id');

        if (!$hook_ids = Multishop::getShopHookIds()) :
        elseif (is_singular() && (in_array(get_the_ID(), $hook_ids))) :
            $shop_id = get_the_ID();
        endif;

        return $shop_id;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Vérifie si la gestion multi-boutique est active (au moins une boutique déclarée)
     */
    /*
    final public static function has()
    {
        return self::getShops();
    }*/

    /**
     * Définition de la boutique par défaut
     */
    final public static function getDefault()
    {
        if (!$default = get_option('tify_wc_multi_default', '')) {
            return;
        }

        return self::getShop($default);
    }

    /**
     * Récupération de la boutique courante
     */
    final public static function getCurrentShop()
    {
        foreach (self::getShops() as $shop) :
            if ($shop->isIn()) :
                return $shop;
            endif;
        endforeach;

        if ($shop = self::getDefault()) :
            return $shop;
        endif;
    }

    /**
     * Récupération de l'identifiant de la boutique courante
     */
    final public static function getCurrentShopId()
    {
        if (!$current = self::getCurrentShop()) {
            return;
        }

        return $current->getId();
    }

    /**
     * Vérifie si la page courante fait partie de l'ecosystème de la boutique
     */
    final public static function inShop($shop_id)
    {
        if (!$shop = self::getShop($shop_id)) {
            return false;
        }

        return $shop->isIn();
    }

    /**
     * Récupération des pages d'accroche des boutiques
     */
    final public static function getShopHookIds()
    {
        $hookIds = [];
        foreach (self::getShops() as $shop_id => $factory) :
            if (!$hook_id = $factory->getHookId()) {
                continue;
            }
            $hookIds[$shop_id] = $hook_id;
        endforeach;

        return $hookIds;
    }

    /**
     * Récupération la page d'accroche d'une boutique
     */
    final public static function getShopHookId($shop_id)
    {
        if ($shop = self::getShop($shop_id)) {
            return $shop->getHookId();
        }
    }

    /**
     * Récupération de la page d'accroche de la boutique courante
     */
    final public static function getCurrentShopHookId()
    {
        if (!$current = self::getCurrentShop()) {
            return;
        }

        return $current->getHookId();
    }

    /**
     * Vérifie si la page courante est l'accroche d'une boutique
     */
    final public static function isShopHook($shop_id = null)
    {
        if (!$hook_id = (int)get_query_var('tify_hook_id', 0)) {
            return false;
        }

        if (!$shop_id) :
            return in_array($hook_id, self::getShopHookIds());
        elseif ($shop_hook_id = self::getShopHookId($shop_id)) :
            return $hook_id == $shop_hook_id;
        endif;
    }

    /**
     * Récupération des catégories d'affichage de toutes les boutiques
     */
    final public static function getShopTermIds()
    {
        $termIds = [];
        foreach (self::getShops() as $shop_id => $factory) :
            if (!$term_id = $factory->getTermId()) {
                continue;
            }
            $termIds[$shop_id] = $term_id;
        endforeach;

        return $termIds;
    }

    /**
     * Récupération l'identifiant de la catégorie d'affichage d'une boutique
     */
    final public static function getShopTermId($shop_id)
    {
        if ($shop = self::getShop($shop_id)) {
            return $shop->getTermId();
        }
    }

    /**
     * Récupération la catégorie d'affichage d'une boutique.
     *
     * @return \WP_Term
     */
    final public static function getShopTerm($shop_id)
    {
        if ($shop = self::getShop($shop_id)) {
            return $shop->getTerm();
        }
    }

    /**
     * Récupération de l'identifiant de la catégorie d'affichage de la boutique courante
     */
    final public static function getCurrentShopTermId()
    {
        if (!$current = self::getCurrentShop()) {
            return;
        }

        return $current->getTermId();
    }

    /**
     * Récupération de la catégorie d'affichage de la boutique courante
     */
    final public static function getCurrentShopTerm()
    {
        if (!$current = self::getCurrentShop()) {
            return;
        }

        return $current->getTerm();
    }
}