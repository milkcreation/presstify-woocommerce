<?php

namespace tiFy\Plugins\Woocommerce\Shortcodes;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Shortcodes as ShortcodesContract;

/**
 * GESTION DES SHORTCODES
 * Permet de désactiver l'exécution des shortcodes dans l'éditeur et de les lancer en dehors
 * ex: echo \tiFy\Plugins\Woocommerce\Shortcodes::doing( 'my_account' );
 *
 * @see https://docs.woocommerce.com/document/woocommerce-shortcodes/
 * @see https://docs.woocommerce.com/document/shortcodes/
 */
class Shortcodes extends ParamsBag implements ShortcodesContract
{
    /**
     * Listes des shortcodes Woocommerce
     */
    protected static $Shortcodes = [
        'woocommerce_cart' => true, 'woocommerce_checkout' => true, 'woocommerce_order_tracking' => true, 'woocommerce_my_account' => true
    ];

    /**
     * CONSTRUCTEUR
     */
    public function __construct($shortcodes = [])
    {
        parent::__construct($shortcodes);

        return;

        // Désactivation de l'éxecution du shortcode Woocommerce dans le contenu de page
        add_filter('pre_do_shortcode_tag', [$this, 'pre_do_shortcode_tag'], 10, 4);
        
        foreach ($shortcodes as $shortcode => $enabled) :
            if (!$enabled) :
                Shortcodes::disable($shortcode);
            endif;
        endforeach;
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Désactivation de l'éxecution du shortcode Woocommerce dans le contenu de page
     */
    public function pre_do_shortcode_tag($output, $tag, $attr, $m)
    {
        if (!in_array($tag, array_keys(self::$Shortcodes)))
            return $output;
        if (!in_the_loop())
            return $output;
        if (self::$Shortcodes[$tag])
            return $output;

        return '';
    }

    /**
     * CONTROLEURS
     */
    /**
     * Désactivation d'un shortcode
     */
    public static function disable($shortcode)
    {
        // Bypass
        if (!in_array($shortcode, array_keys(self::$Shortcodes)))
            return;

        self::$Shortcodes[$shortcode] = false;
    }

    /**
     * Execution d'un shortcode en dehors de la boucle
     * @see class-wc-shortcodes.php
     */
    public static function doing($shortcode, $attrs = [])
    {
        if (preg_match('/^woocommerce_(.*)/', $shortcode, $matches)) :
        else :
            $shortcode = 'woocommerce_' . $shortcode;
        endif;

        // Bypass
        if (!in_array($shortcode, array_keys(self::$Shortcodes)))
            return;

        $map = [
            'woocommerce_order_tracking' => 'WC_Shortcodes::order_tracking',
            'woocommerce_cart'           => 'WC_Shortcodes::cart',
            'woocommerce_checkout'       => 'WC_Shortcodes::checkout',
            'woocommerce_my_account'     => 'WC_Shortcodes::my_account',
        ];

        if (!isset($map[$shortcode]))
            return;

        return call_user_func($map[$shortcode], $attrs);
    }
}