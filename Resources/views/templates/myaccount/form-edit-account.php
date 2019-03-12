<?php
/**
 * Woccommerce - Mon Compte - Modification détails du compte
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.5.0
 *
 * @var WP_User $current_user
 * @var App\Views\ViewController $this
 */

$this->layout('wc.myaccount::my-account');
?>

<section class="MyAccountDetails">
    <div class="row">
        <div class="col-12 col-xl-7">
            <form class="MyAccountDetails-form" action="" method="post">

                <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
                <input type="hidden" name="action" value="save_account_details"/>

                <h3 class="Woocommerce-title"><?php _e('Details du compte', 'theme'); ?></h3>

                <div class="MyAccountDetails-formFields clearfix">
                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--50">
                        <label for="account_first_name" class="woocommerce-Label">
                            <?php _e('Prénom *', 'theme'); ?>
                        </label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text"
                               name="account_first_name" id="account_first_name" autocomplete="given-name"
                               value="<?php echo esc_attr($current_user->first_name); ?>"/>
                    </div>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--50">
                        <label for="account_last_name" class="woocommerce-Label">
                            <?php _e('Nom de famille *', 'theme'); ?>
                        </label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text"
                               name="account_last_name" id="account_last_name" autocomplete="family-name"
                               value="<?php echo esc_attr($current_user->last_name); ?>"/>
                    </div>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="account_display_name" class="woocommerce-Label">
                            <?php _e('Nom à afficher *', 'theme'); ?>
                        </label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text"
                               name="account_display_name" id="account_display_name"
                               value="<?php echo esc_attr($current_user->display_name); ?>"/>
                    </div>

                    <p class="MyAccountDetails-formNotice">
                        <?php _e('Correspond à votre nom d\'affichage sur le site.', 'theme'); ?>
                    </p>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="account_email" class="woocommerce-Label">
                            <?php _e('Adresse e-mail *', 'theme'); ?>
                        </label>
                        <input type="email" class="woocommerce-Input woocommerce-Input--email" name="account_email"
                               id="account_email" autocomplete="email"
                               value="<?php echo esc_attr($current_user->user_email); ?>"/>
                    </div>
                </div>

                <div class="MyAccountDetails-formPassword clearfix">
                    <h3 class="Woocommerce-title"><?php _e('Modification du mot de passe', 'theme'); ?></h3>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="password_current" class="woocommerce-Label">
                            <?php _e('Mot de passe actuel (laissez vide pour ne pas changer)', 'theme'); ?>
                        </label>
                        <input type="password" class="woocommerce-Input woocommerce-Input--password"
                               name="password_current" id="password_current" autocomplete="off"/>
                    </div>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="password_1" class="woocommerce-Label">
                            <?php _e('Nouveau mot de passe (laissez vide pour ne pas changer)', 'theme'); ?>
                        </label>
                        <input type="password" class="woocommerce-Input woocommerce-Input--password"
                               name="password_1" id="password_1" autocomplete="off"/>
                    </div>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="password_2" class="woocommerce-Label">
                            <?php _e('Confirmez le nouveau mot de passe', 'theme'); ?>
                        </label>
                        <input type="password" class="woocommerce-Input woocommerce-Input--password"
                               name="password_2" id="password_2" autocomplete="off"/>
                    </div>
                </div>

                <button type="submit" class="MyAccountDetails-formLink Readmore Readmore--1" name="save_account_details"
                        value="<?php esc_attr_e('Save changes', 'woocommerce'); ?>">
                    <span class="Readmore-inner Readmore-inner--1">
                        <?php _e('Valider les changements', 'theme'); ?>
                    </span>
                </button>

            </form>
        </div>
    </div>
</section>