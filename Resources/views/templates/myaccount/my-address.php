<?php
/**
 * Woccommerce - Mon Compte - Adresses
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 2.6.0
 *
 * @var array $get_addresses
 * @var App\Views\ViewController $this
 */

$this->layout('wc.myaccount::my-account');
?>

<section class="MyAccountAddress">

    <div class="MyAccountAddress-notice">
        <?php echo apply_filters(
            'woocommerce_my_account_my_address_description',
            __('The following addresses will be used on the checkout page by default.', 'woocommerce')
        ); ?>
    </div>

    <div class="row">

        <?php foreach ($get_addresses as $name => $title) : ?>

        <div class="col-12 col-lg-6">

            <div class="MyAccountAddress-details">

                <header class="MyAccountAddress-detailsTitle">
                    <h3 class="Woocommerce-title"><?php echo $title; ?></h3>
                </header>

                <address class="MyAccountAddress-detailsContent">
                    <?php
                    $address = wc_get_account_formatted_address($name);
                    echo $address
                        ? wp_kses_post($address)
                        : __('Vous n\'avez pas encore renseigné cette adresse.', 'theme');
                    ?>
                </address>

                <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $name)); ?>"
                   class="MyAccountAddress-detailsLink Readmore Readmore--1">
                    <span class="Readmore-inner Readmore-inner--1">
                        <?php _e('Modifier l\'adresse', 'theme'); ?>
                    </span>
                </a>

            </div>
        </div>

        <?php endforeach; ?>
    </div>
</section>