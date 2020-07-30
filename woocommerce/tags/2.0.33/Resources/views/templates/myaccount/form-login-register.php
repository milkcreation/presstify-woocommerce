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
        <?php _e('Nouveau client ?', 'tify'); ?>
    </h3>

    <div class="MyAccountAuthenticate-registerContent">
        <p class="MyAccountAuthenticate-registerText">
            <?php _e('Nos clients bénéficient de nombreux services comme :', 'tify'); ?>
        </p>

        <ul class="MyAccountAuthenticate-registerItems">
            <li class="MyAccountAuthenticate-registerItem">
                <?php _e('L\'accès à des avantages spécifiques', 'tify'); ?>
            </li>
            <li class="MyAccountAuthenticate-registerItem">
                <?php _e('L\'historique des commandes', 'tify'); ?>
            </li>
            <li class="MyAccountAuthenticate-registerItem">
                <?php _e('La liste des produits préférés', 'tify'); ?>
            </li>
        </ul>

        <p class="MyAccountAuthenticate-registerText">
            <?php _e('et plus encore...', 'tify'); ?>
        </p>

        <a href="<?php echo router()->url('wc.myaccount.register', [], false); ?>"
           class="MyAccountAuthenticate-registerLink Readmore Readmore--3">
            <span class="Readmore-inner Readmore-inner--3">
                <?php _e('Créer mon compte', 'tify'); ?>
            </span>
        </a>
    </div>
</div>