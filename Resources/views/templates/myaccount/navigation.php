<?php
/**
 * Affichage du menu de navigation.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var App\Views\ViewController $this
 *
 * @version 2.6.0
 */
?>

<nav class="MyAccountNavigation">

    <ul class="MyAccountNavigation-items">

        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>

            <li class="MyAccountNavigation-item MyAccountNavigation-item--<?php echo $endpoint; ?>">

                <?php if ($endpoint == 'customer-logout'): ?>

                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"
                       class="MyAccountNavigation-itemLink MyAccountNavigation-itemLink--<?php echo $endpoint; ?>
                       Readmore Readmore--1">
                        <span class="Readmore-inner Readmore-inner--1">
                            <?php echo esc_html($label); ?>
                        </span>
                    </a>

                <?php else: ?>

                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"
                       class="MyAccountNavigation-itemLink MyAccountNavigation-itemLink--<?php echo $endpoint; ?>">
                        <span class="MyAccountNavigation-itemLinkText">
                            <?php echo esc_html($label); ?>
                        </span>
                    </a>

                <?php endif; ?>

            </li>

        <?php endforeach; ?>

    </ul>

</nav>