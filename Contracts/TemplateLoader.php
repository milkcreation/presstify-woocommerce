<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface TemplateLoader extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * Chargement d'un template Woocommerce.
     *
     * @param string $template Chemin absolu du fichier du gabarit à charger.
     * @param array $args Liste des attributs passés en arguments.
     *
     * @return void
     */
    public function loadWcTemplate(string $template = '', array $args = []): void;
}