<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Shortcodes extends ParamsBag
{
    /**
     * Désactivation d'un shortcode.
     *
     * @param string $shortcode Nom du shortcode.
     *
     * @return void
     */
    public function disable($shortcode);

    /**
     * Execution d'un shortcode Woocommerce en dehors de la boucle.
     *
     * @param string $shortcode Nom du shortcode.
     * @param array $attrs Attributs du shortcode.
     *
     * @see class-wc-shortcodes.php
     *
     * @return mixed
     */
    public function doing($shortcode, $attrs = []);
}