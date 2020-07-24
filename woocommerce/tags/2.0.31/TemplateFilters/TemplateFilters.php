<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\TemplateFilters;

use tiFy\Plugins\Woocommerce\{Contracts\TemplateFilters as TemplateFiltersContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;

/**
 * @see Woocommerce/includes/wc-template-functions.php
 */
class TemplateFilters extends ParamsBag implements TemplateFiltersContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_filter('woocommerce_breadcrumb_defaults', [$this, 'woocommerce_breadcrumb_defaults']);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function parse(): TemplateFiltersContract
    {
        parent::parse();

        return $this;
    }

    /**
     * Exemple de court-circuitage des attributs du fil d'Ariane.
     *
     * @param array $args {
     *      @var $delimiter, '&nbsp;&#47;&nbsp;' par défaut.
     *      @var $wrap_before, '<nav class="woocommerce-breadcrumb">' par défaut.
     *      @var $wrap_after, '</nav>' par défaut.
     *      @var $before, '' par défaut.
     *      @var $after, '' par défaut.
     *      @var $home, _x( 'Home', 'breadcrumb', 'woocommerce' ) par défaut.
     * }
     *
     * @return array
     */
    public function woocommerce_breadcrumb_defaults(array $args = []) : array
    {
        return $args;
    }
}