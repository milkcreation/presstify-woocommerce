<?php
namespace tiFy\Plugins\WooCommerce\MultiShop\Taboox\Option\MultiShopGeneralOptions\Admin;

use tiFy\Core\Field\Field;

class MultiShopGeneralOptions extends \tiFy\Core\Taboox\Options\Admin
{
    public function init()
    {
        add_filter('pre_option_woocommerce_shop_page_display', [$this, 'pre_option_woocommerce_shop_page_display'], 10, 2);
    }

    public function pre_option_woocommerce_shop_page_display($value, $option_name)
    {
        // Bypass
        if (is_customize_preview()) :
            return $value;
        endif;

        if($shop_id = tify_wc_multi_current_shop_id()) :
            $value = get_option('tify_wc_'. $shop_id .'_page_display_cat', 'products');
        endif;

        return $value;
    }

    /**
     * Initialisation de l'interface d'administration
     */
    public function admin_init()
    {
        \register_setting($this->page, 'tify_wc_'. $this->args['shop_id'] .'_hook');
        \register_setting($this->page, 'tify_wc_'. $this->args['shop_id'] .'_cat');
        \register_setting($this->page, 'tify_wc_'. $this->args['shop_id'] .'_page_display', ['sanitize_callback' => [$this, 'sanitize_tify_wc_page_display']]);
        \register_setting($this->page, 'tify_wc_multi_default');
    }
    
    /**
     * CONTROLEURS
     */
    /**
     * Formulaire de saisie
     */
    public function form()
    {
?>
<table class="form-table">
    <tbody>
        <tr>
            <th><?php _e( 'Page d\'affichage de la boutique', 'tify' );?></th>
            <td>
            <?php 
                wp_dropdown_pages(
                    array(
                        'name'              => 'tify_wc_'. $this->args['shop_id'] .'_hook', 
                        'hierarchical'      => 1,
                        'show_option_none'  => __( 'Aucune page choisie', 'tify' ),
                        'option_none_value' => '',
                        'show_count'        => 1,
                        'selected'          => get_option( 'tify_wc_'. $this->args['shop_id'] .'_hook', 0 ),
                        'sort_order'        => 'ASC',
                        'sort_column'       => 'menu_order'
                    )
                );
            ?>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Boutique par défaut', 'tify' );?></th>
            <td>
                <input type="checkbox" value="<?php echo $this->args['shop_id'];?>" name="tify_wc_multi_default" <?php checked( $this->args['shop_id'] === get_option( 'tify_wc_multi_default', '' ) );?> style="vertical-align:top" /> 
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Catégorie affichée par la boutique', 'tify' );?></th>
            <td>
            <?php 
                wp_dropdown_categories(
                    array(
                        'name'              => 'tify_wc_'. $this->args['shop_id'] .'_cat', 
                        'taxonomy'          => 'product_cat', 
                        'hierarchical'      => 1,
                        'show_option_none'  => __( 'Aucune catégorie choisie', 'tify' ),
                        'option_none_value' => 0,
                        'hide_empty'        => false,
                        'show_count'        => 1,
                        'selected'          => get_option('tify_wc_'. $this->args['shop_id'] .'_cat', 0)
                    )
                );
            ?>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Affichage de la page', 'tify' );?></th>
            <td>
            <?php
            echo Field::Select(
                    [
                        'name'      => 'tify_wc_'. $this->args['shop_id'] .'_page_display',
                        'value'     => get_option('tify_wc_'. $this->args['shop_id'] .'_page_display', 'products'),
                        'options'   => [
                            'products'      => __('Afficher les produits', 'tify'),
                            'subcategories' =>  __('Afficher les catégories', 'tify'),
                            'both'          =>  __('Afficher les catégories et les produits', 'tify'),
                        ]
                    ]
                );
            ?>
            </td>
        </tr>
    </tbody>
</table>
<?php    
    }

    public function sanitize_tify_wc_page_display($value)
    {
        // Récupération de la catégorie affichée par la boutique
        $cat = (int)get_option('tify_wc_'. $this->args['shop_id'] .'_cat', 0);

        // Récupération de la catégorie liée à l'affichage de la page
        if ($page_display_cat = (int)get_option('tify_wc_'. $this->args['shop_id'] .'_page_display_cat', 0) ) :
            if ($cat !== $page_display_cat) :
                delete_term_meta($page_display_cat, 'display_type');
            endif;
        endif;

        update_option('tify_wc_'. $this->args['shop_id'] .'_page_display_cat', $cat);
        update_term_meta($cat, 'display_type', $value);

        return $value;
    }
}