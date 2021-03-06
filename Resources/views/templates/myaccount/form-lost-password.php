<?php
/**
 * Woccommerce - Mon Compte - Mot de passe perdu
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.4.0
 *
 * @var App\Views\ViewController $this
 */

$this->skeletonLayoutBody();
$this->skeletonBlocks();
?>

<section class="MyAccountPassword-lost">
    <form method="post" class="MyAccountPassword-lostForm">
        <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
        <input type="hidden" name="wc_reset_password" value="true" />

        <div class="MyAccountPassword-lostNotice">
            <?php _e('Vous avez perdu votre mot de passe ? Entrez votre identifiant ou adresse e-mail. 
            Vous allez recevoir un lien par e-mail pour le rĂ©initialiser.', 'tify'); ?>
        </div>

        <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
            <label for="user_login" class="woocommerce-Label">
                <?php _e( 'Identifiant ou e-mail *', 'tify'); ?>
            </label>
            <input class="woocommerce-Input woocommerce-Input--text" type="text"
                   name="user_login" id="user_login" autocomplete="username" />
        </div>

        <p class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
            <button type="submit" class="MyAccountPassword-lostLink Readmore Readmore--3"
                    value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
                <span class="Readmore-inner Readmore-inner--3">
                    <?php _e( 'RĂ©initialiser le mot de passe', 'tify'); ?>
                </span>
            </button>
        </p>
    </form>
</section>