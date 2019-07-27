<?php
/**
 * Fiche produit - Prix > Affichage par défaut.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\View\ViewController $this
 * @var tiFy\Plugins\Woocommerce\Contracts\QueryProduct $product
 */
?>
<div class="ProductPrices-total">
    <?php if ($product->isOnSale()) : ?>
        <div class="ProductPrices-title">
            <?php _e('A partir de', 'theme'); ?>
        </div>
    <?php endif; ?>

    <div class="ProductPrices-price ProductPrices-price--with_tax">
        <span class="ProductPrices-priceValue">
            <?php echo $product->getPriceIncludingTax() . get_woocommerce_currency_symbol(); ?>
        </span>
        <span class="ProductPrices-priceSuffix"><?php _e('TTC', 'theme'); ?></span>
    </div>

    <div class="ProductPrices-price ProductPrices-price--without_tax">
        <span class="ProductPrices-priceValue">
            <?php echo $product->getPriceExcludingTax() . get_woocommerce_currency_symbol(); ?>
        </span>
        <span class="ProductPrices-priceSuffix"><?php _e('HT', 'theme'); ?></span>
    </div>

    <?php if ($product->isSimple()) : ?>
    <div class="ProductPrices-stock">
        <?php echo wc_get_stock_html($product->getWcProduct()); ?>
    </div>
    <?php endif; ?>
</div>
