<?php

namespace tiFy\Plugins\Woocommerce\Shortcodes;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Shortcodes as ShortcodesContract;

/**
 * GESTION DES SHORTCODES
 * Permet de désactiver l'exécution des shortcodes dans l'éditeur et de les lancer en dehors
 *
 * @see https://docs.woocommerce.com/document/woocommerce-shortcodes/
 * @see https://docs.woocommerce.com/document/shortcodes/
 */
class Shortcodes extends ParamsBag implements ShortcodesContract
{
    /**
     * Listes des shortcodes WooCommerce.
     * @var array
     */
    protected $shortcodes = [
        'woocommerce_cart'           => true,
        'woocommerce_checkout'       => true,
        'woocommerce_order_tracking' => true,
        'woocommerce_my_account'     => true
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($shortcodes = [])
    {
        parent::__construct($shortcodes);

        $this->disablePostContentShortcodes();
        $this->disableShortcodes($this->all());
    }

    /**
     * Désactivation de l'éxecution du shortcode WooCommerce dans le contenu de page.
     *
     * @return void
     */
    public function disablePostContentShortcodes()
    {
        add_action(
            'pre_do_shortcode_tag',
            function ($output, $tag, $attr, $m) {
                if (!in_array($tag, array_keys($this->shortcodes)) || !in_the_loop() || $this->shortcodes[$tag]) :
                    return $output;
                endif;

                return null;
            },
            10,
            4
        );
    }

    /**
     * Désactivation des shortcodes.
     *
     * @param array $shortcodes Liste des shortcodes.
     *
     * @return void
     */
    public function disableShortcodes($shortcodes)
    {
        foreach ($shortcodes as $shortcode => $enabled) :
            if (!$enabled) :
                $this->disable($shortcode);
            endif;
        endforeach;
    }

    /**
     * Désactivation d'un shortcode.
     *
     * @param string $shortcode Nom du shortcode.
     *
     * @return void
     */
    public function disable($shortcode)
    {
        // Bypass
        if (!in_array($shortcode, array_keys($this->shortcodes))) :
            return null;
        endif;
        $this->shortcodes[$shortcode] = false;
    }

    /**
     * Execution d'un shortcode WooCommerce en dehors de la boucle.
     *
     * @param string $shortcode Nom du shortcode.
     * @param array $attrs Attributs du shortcode.
     *
     * @see class-wc-shortcodes.php
     *
     * @return mixed
     */
    public function do($shortcode, $attrs = [])
    {
        if (preg_match('/^woocommerce_(.*)/', $shortcode, $matches)) :
        else :
            $shortcode = 'woocommerce_' . $shortcode;
        endif;

        // Bypass
        if (!in_array($shortcode, array_keys($this->shortcodes))) :
            return null;
        endif;

        $map = [
            'woocommerce_order_tracking' => 'WC_Shortcodes::order_tracking',
            'woocommerce_cart'           => 'WC_Shortcodes::cart',
            'woocommerce_checkout'       => 'WC_Shortcodes::checkout',
            'woocommerce_my_account'     => 'WC_Shortcodes::my_account',
        ];

        if (!isset($map[$shortcode])) :
            return null;
        endif;

        return call_user_func($map[$shortcode], $attrs);
    }
}