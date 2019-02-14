<?php
/**
 * Woocommerce - Mon Compte - Tableau de bord
 * ---------------------------------------------------------------------------------------------------------------------
 * @version     2.6.0
 *
 * @var App\Views\ViewController $this
 */
?>

<?php $this->layout('wc.myaccount::my-account'); ?>

<section class="MyAccountDashboard">
    <p class="MyAccountDashboard-title">
        <?php
        printf(
            __('Bonjour <strong>%1$s</strong> <br>(Vous n\'êtes pas %1$s? <a href="%2$s">Déconnexion</a>)', 'theme'),
            esc_html($current_user->display_name),
            esc_url(wc_logout_url(wc_get_page_permalink('myaccount')))
        );
        ?>
    </p>

    <div class="Dashboard-items">
        <ul class="row">
            <li class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="Dashboard-itemInner">
                    <?php _e('Consulter mes commandes', 'theme'); ?>
                    <a href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>" class="Dashboard-itemLink"
                       title="<?php _e('Consulter mes commandes', 'theme') ?>"></a>
                </div>
            </li>

            <li class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="Dashboard-itemInner">
                    <?php _e('Consulter mes adresses', 'theme'); ?>
                    <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address')); ?>" class="Dashboard-itemLink"
                       title="<?php _e('Consulter mes adresses', 'theme'); ?>"></a>
                </div>
            </li>

            <li class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="Dashboard-itemInner">
                    <?php _e('Consulter le détail de mon compte', 'theme'); ?>
                    <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>" class="Dashboard-itemLink"
                       title="<?php _e('Consulter le détail de mon compte', 'theme'); ?>"></a>
                </div>
            </li>
        </ul>
    </div>

    <?php echo $this->accountReadmore(); ?>
</section>