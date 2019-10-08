<?php

namespace tiFy\Plugins\Woocommerce\Routing\ProductCat;

use tiFy\Contracts\View\ViewEngine;

class RadioWalker extends \Walker_Category_Checklist
{
    /**
     * Instance du moteur de gabarits d'affichage.
     * @return ViewEngine
     */
    protected $viewer;

    public $tree_type = 'category';
    public $db_fields = ['parent' => 'parent', 'id' => 'term_id'];

    /**
     * CONSTRUCTEUR.
     *
     * @param \tiFy\View\ViewEngine $viewer Instance du moteur de gabarits d'affichage.
     */
    public function __construct(ViewEngine $viewer)
    {
        $this->viewer = $viewer;
    }

    /**
     * {@inheritdoc}
     */
    public function start_el(&$output, $term, $depth = 0, $args = [], $id = 0)
    {
        $selected = $args['selected_cats'] ?? [];

        $output .= (string) $this->viewer->make('_override::radio-item', compact('selected', 'term'));
    }
}
