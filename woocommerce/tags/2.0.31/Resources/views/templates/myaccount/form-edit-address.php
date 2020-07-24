<?php
/**
 * Woccommerce - Mon Compte - Modification adresses
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.4.0
 *
 * @var string $page_title
 * @var array $address
 * @var App\Views\ViewController $this
 */

$this->layout('wc.myaccount::my-account');
?>

<section class="MyAccountAddress-edit">
    <div class="row">
        <div class="col-12 col-xl-8">

            <h3 class="Woocommerce-title"><?php echo $page_title; ?></h3>

            <form action="" method="post" class="MyAccountAddress-editForm">
                <?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
                <input type="hidden" name="action" value="edit_address"/>

                <div class="MyAccountAddress-editFields">
                    <div class="MyAccountAddress-editWrapper clearfix">
                        <?php
                        foreach ($address as $key => $field) :
                            woocommerce_form_field($key, $field, wc_get_post_data_by_key($key, $field['value']));
                        endforeach;
                        ?>
                    </div>

                    <button type="submit" class="MyAccountAddress-editLink Readmore Readmore--1" name="save_address"
                            value="<?php esc_attr_e('Save address', 'woocommerce'); ?>">
                        <span class="Readmore-inner Readmore-inner--1">
                            <?php _e('Valider les changements', 'tify'); ?>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>