<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface Shortcodes extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Désactivation d'un shortcode.
     *
     * @param string $tag Nom de qualification du shortcode.
     *
     * @return void
     */
    public function disable(string $tag): void;

    /**
     * Execution d'un shortcode Woocommerce en dehors de la boucle.
     *
     * @param string $tag Nom de qualification du shortcode.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @see class-wc-shortcodes.php
     *
     * @return string
     */
    public function doing(string $tag, array $attrs = []): ?string;

    /**
     * @inheritDoc
     */
    public function parse(): Shortcodes;
}