<?php
/**
 * @var tiFy\Plugins\Woocommerce\Contracts\StoreFactory $store
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <label for="Woocommerce-storeDefault--<?php echo $store->getName(); ?>">
                <?php _e('Utiliser en tant que boutique par défaut', 'tify'); ?>
            </label>
        </th>
        <td>
            <?php echo field('checkbox', [
                'attrs'   => [
                    'id'    => "Woocommerce-storeDefault--{$store->getName()}",
                    'style' => 'vertical-align:top',
                ],
                'checked' => $store->getName() === get_option('tify_wc_multi_default', ''),
                'name'    => 'tify_wc_multi_default',
                'value'   => $store->getName(),
            ]); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Page d\'affichage de la boutique', 'tify'); ?></th>
        <td>
            <?php wp_dropdown_pages([
                'name'              => 'tify_wc_' . $store->getName() . '_hook',
                'hierarchical'      => 1,
                'show_option_none'  => __('Aucune page choisie', 'tify'),
                'option_none_value' => '',
                'show_count'        => 1,
                'selected'          => get_option('tify_wc_' . $store->getName() . '_hook', 0),
                'sort_order'        => 'ASC',
                'sort_column'       => 'menu_order',
            ]); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Catégorie de produit associé à la boutique', 'tify'); ?></th>
        <td>
            <?php wp_dropdown_categories([
                'name'              => 'tify_wc_' . $store->getName() . '_cat',
                'taxonomy'          => 'product_cat',
                'hierarchical'      => 1,
                'show_option_none'  => __('Aucune catégorie choisie', 'tify'),
                'option_none_value' => 0,
                'hide_empty'        => false,
                'show_count'        => 1,
                'selected'          => get_option('tify_wc_' . $store->getName() . '_cat', 0),
            ]); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Affichage de la page', 'tify'); ?></th>
        <td>
            <?php echo field('select', [
                'name'    => 'tify_wc_' . $store->getName() . '_page_display',
                'value'   => get_option('tify_wc_' . $store->getName() . '_page_display', 'products'),
                'choices' => [
                    'products'      => __('Afficher les produits', 'tify'),
                    'subcategories' => __('Afficher les catégories', 'tify'),
                    'both'          => __('Afficher les catégories et les produits', 'tify'),
                ],
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>