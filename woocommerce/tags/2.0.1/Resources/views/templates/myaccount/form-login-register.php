<?php
/**
 * Mon Compte - Authentification - Créer un compte
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @var App\Views\ViewController $this
 */
?>

<div class="MyAccountAuthenticate-register">
    <h3 class="Woocommerce-title">
        <?php _e('Nouveau client ?', 'theme'); ?>
    </h3>

    <div class="MyAccountAuthenticate-registerContent">
        <p class="MyAccountAuthenticate-registerText">
            <?php _e('Nos clients bénéficient de nombreux services comme :', 'theme'); ?>
        </p>

        <ul class="MyAccountAuthenticate-registerItems">
            <li class="MyAccountAuthenticate-registerItem">
                <?php _e('L\'accès à des avantages spécifiques', 'theme'); ?>
            </li>
            <li class="MyAccountAuthenticate-registerItem">
                <?php _e('L\'historique des commandes', 'theme'); ?>
            </li>
            <li class="MyAccountAuthenticate-registerItem">
                <?php _e('La liste des produits préférés', 'theme'); ?>
            </li>
        </ul>

        <p class="MyAccountAuthenticate-registerText">
            <?php _e('et plus encore...', 'theme'); ?>
        </p>

        <a href="<?php echo router()->url('wc.myaccount.register', [], false); ?>"
           class="MyAccountAuthenticate-registerLink Readmore Readmore--3">
            <span class="Readmore-inner Readmore-inner--3">
                <?php _e('Créer mon compte', 'theme'); ?>
            </span>
        </a>
    </div>
</div>