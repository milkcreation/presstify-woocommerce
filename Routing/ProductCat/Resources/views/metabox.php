<?php
/**
 * Gestion de la catégorie principale des produits.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var App\Views\ViewController $this
 * @var \WP_Taxonomy $taxonomy
 * @var \WP_Term[] $terms
 * @var \App\Woocommerce\ProductCat\RadioWalker $radioWalker
 * @var int $selected
 * @var string $route_name
 */
?>
<div class="PostTerms" data-ajax_nonce="<?php echo esc_attr(wp_create_nonce('theme_metabox_item_change_permalink')); ?>"
     data-tax_name="<?php echo esc_attr($taxonomy->name); ?>"
     data-route_name="<?php echo esc_attr($route_name); ?>">
    <?php
    echo field(
        'text',
        [
            'attrs' => [
                'placeholder' => __('url du produit', 'tify'),
                'class'       => 'widefat PostTerms-url',
                'readonly'
            ]
        ]
    );
    ?>
    <h3 class="section_title"><?php _e('Choix de la catégorie principale (utilisée pour l\'url)', 'tify'); ?></h3>
    <ul class="PostTerms-items PostTerms-items--main">
        <li class="PostTerms-item--none">
            <label>
                <?php
                echo field(
                    'radio',
                    [
                        'name'    => '_main_post_term',
                        'value'   => 0,
                        'checked' => !$selected,
                        'after'   => __('Aucune', 'tify')
                    ]
                )
                ?>
            </label>
        </li>
        <?php
        wp_terms_checklist(
            $post->ID,
            [
                'taxonomy'      => $taxonomy->name,
                'walker'        => $radioWalker,
                'selected_cats' => [$selected]
            ]
        );
        ?>
    </ul>
    <h3 class="section_title"><?php _e('Choix des catégories associées au produit', 'tify'); ?></h3>
    <ul class="PostTerms-items PostTerms-items--all">
        <?php
        wp_terms_checklist(
            $post->ID,
            [
                'taxonomy' => $taxonomy->name
            ]
        );
        ?>
    </ul>
</div>
