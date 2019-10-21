<?php
/**
 * Woccommerce - Mon Compte - Authentification
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.5.0
 *
 * @var App\Views\ViewController $this
 */
$this->skeletonLayoutBody();
$this->skeletonBlocks();

do_action('woocommerce_before_customer_login_form'); ?>

<section class="MyAccountAuthenticate" id="customer_login">

    <div class="row">

        <?php if (get_option('woocommerce_enable_myaccount_registration') === 'yes') : ?>

        <div class="col-12 col-lg-6">

            <?php $this->insert('wc.myaccount::form-login-register'); ?>

            <?php /*
                <div class="MyAccountAuthenticate-register">

                    <h2 class="Woocommerce-title"><?php _e('Inscrivez-vous', 'tify'); ?></h2>

                    <form method="post" class="MyAccountAuthenticate-registerForm clearfix">

                        <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>

                            <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                                <label for="reg_username" class="Woocommerce-label">
                                    <?php _e('Nom d\'utilisateur *', 'tify'); ?>
                                </label>
                                <input type="text" class="woocommerce-Input woocommerce-Input--text" name="username"
                                       id="reg_username" autocomplete="username"
                                       value="<?php echo (!empty($_POST['username'])) ?
                                           esc_attr(wp_unslash($_POST['username'])) : ''; ?>"/>
                            </div>

                        <?php endif; ?>

                        <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                            <label for="reg_email" class="Woocommerce-label">
                                <?php _e('Adresse e-mail *', 'tify'); ?>
                            </label>
                            <input type="email" class="woocommerce-Input woocommerce-Input--text"
                                   name="email" id="reg_email" autocomplete="email"
                                   value="<?php echo (!empty($_POST['email'])) ?
                                       esc_attr(wp_unslash($_POST['email'])) : ''; ?>"/>
                        </div>

                        <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>

                            <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                                <label for="reg_password" class="Woocommerce-label">
                                    <?php _e('Mot de passe *', 'tify'); ?>
                                </label>
                                <input type="password" class="woocommerce-Input woocommerce-Input--text"
                                       name="password" id="reg_password" autocomplete="new-password"/>
                            </div>

                        <?php endif; ?>

                        <?php do_action('woocommerce_register_form'); ?>

                        <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                            <button type="submit" class="MyAccountAuthenticate-registerLink Readmore Readmore--3"
                                    name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>">
                                <span class="Readmore-inner Readmore-inner--3">
                                    <?php _e('Inscription', 'tify'); ?>
                                </span>
                            </button>
                        </div>

                    </form>

                </div> */ ?>

        </div>

        <?php endif; ?>

        <div class="col-12 col-lg-6 col-xl-5">

            <div class="MyAccountAuthenticate-login">

                <h2 class="Woocommerce-title"><?php _e('Connectez vous', 'tify') ?></h2>

                <form class="MyAccountAuthenticate-form clearfix" method="post">

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="username" class="woocommerce-Label">
                            <?php _e('Identifiant ou adresse de messagerie *', 'tify'); ?>
                        </label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text"
                               name="username" id="username" autocomplete="username"
                               value="<?php echo (!empty($_POST['username'])) ?
                                   esc_attr(wp_unslash($_POST['username'])) : ''; ?>"/>
                    </div>

                    <div class="woocommerce-InputWrapper woocommerce-InputWrapper--100">
                        <label for="password" class="woocommerce-Label">
                            <?php _e('Mot de passe *', 'tify'); ?>
                        </label>
                        <input class="woocommerce-Input woocommerce-Input--text" type="password"
                               name="password" id="password" autocomplete="current-password"/>
                    </div>

                    <?php do_action('woocommerce_login_form'); ?>
                    
                    <div class="MyAccountAuthenticate-password">
                        <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                        <div class="MyAccountAuthenticate-passwordRemember">
                            <input class="CheckboxInput"
                                   name="rememberme" type="checkbox" id="rememberme" value="forever"/>
                            <label for="rememberme" class="CheckboxLabel">
                                <span class="CheckboxLabel-text">
                                    <?php _e('Mémoriser', 'tify'); ?>
                                </span>
                            </label>
                        </div>
                        <div class="MyAccountAuthenticate-passwordLost">
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"
                               class="MyAccountAuthenticate-passwordLostLink">
                                <?php _e('Mot de passe perdu?', 'tify'); ?>
                            </a>
                        </div>
                    </div>

                    <button type="submit" class="MyAccountAuthenticate-button Readmore Readmore--3" name="login"
                            value="<?php esc_attr_e('Log in', 'woocommerce'); ?>">
                        <span class="Readmore-inner Readmore-inner--3">
                            <?php _e('Connexion', 'tify'); ?>
                        </span>
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>