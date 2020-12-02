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
}