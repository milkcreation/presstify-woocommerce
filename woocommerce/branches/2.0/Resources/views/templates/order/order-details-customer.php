<?php
/**
 * Woocommerce - Mon Compte - Détails des adresses clients.
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.4.4
 *
 * @var WC_Order $order
 */
?>

<section class="MyAccountOrderDetails-customer">

    <div class="row">

        <div class="col-12 col-xl-6">
            <h2 class="Woocommerce-title">
                <?php _e('Adresse de facturation', 'tify'); ?>
            </h2>

            <address class="MyAccountOrderDetails-customerAddress">
                <?php echo wp_kses_post($order->get_formatted_billing_address(__('N/A', 'woocommerce'))); ?>

                <?php if ($order->get_billing_phone()) : ?>
                    <p class="woocommerce-customer-details--phone">
                        <?php echo esc_html($order->get_billing_phone()); ?>
                    </p>
                <?php endif; ?>

                <?php if ($order->get_billing_email()) : ?>
                    <p class="woocommerce-customer-details--email">
                        <?php echo esc_html($order->get_billing_email()); ?>
                    </p>
                <?php endif; ?>
            </address>

        </div>

        <div class="col-12 col-xl-6">

            <h2 class="Woocommerce-title">
                <?php _e('Adresse de livraison', 'tify'); ?>
            </h2>

            <address class="MyAccountOrderDetails-customerAddress">
                <?php echo wp_kses_post($order->get_formatted_shipping_address(__('N/A', 'woocommerce'))); ?>
            </address>

        </div>

    </div>

</section>
