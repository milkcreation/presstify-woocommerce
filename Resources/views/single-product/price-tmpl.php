<?php
/**
 * Fiche produit - Prix > Gabarit Mustache.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\View\ViewController $this
 * @var tiFy\Plugins\Woocommerce\Contracts\QueryProduct $product
 */
?>
{{#show_total}}
    <div class="ProductPrices-total">
        {{#totalttc}}
        <div class="ProductPrices-price ProductPrices-price--primary">
            <span class="ProductPrices-priceValue">
                {{{ totalttc }}}{{{ currency_symbol }}}<sup>*</sup>
            </span>
            <span class="ProductPrices-priceSuffix">
                <?php _e('TTC', 'theme'); ?>
            </span>
        </div>

        {{/totalttc}}

        {{#totalht}}
        <div class="ProductPrices-price ProductPrices-price--secondary">
            <span class="ProductPrices-priceValue">
            {{{ totalht }}}{{{ currency_symbol }}}<sup>*</sup>
            </span>
            <span class="ProductPrices-priceSuffix">
                <?php _e('HT', 'theme'); ?>
            </span>
        </div>
        {{/totalht}}

        <div class="ProductPrices-stock">
            {{{ availability_html }}}
        </div>
    </div>
{{/show_total}}

{{^show_total}}
    {{#unavailable}}
        <?php echo partial('notice', [
                'type'    => 'warning',
                'content' => __('Le produit n\'est pas disponible dans cette configuration.', 'theme')
            ]);
        ?>
    {{/unavailable}}

    {{^unavailable}}
        <?php $this->insert('single-product/default-price', compact('product')); ?>
    {{/unavailable}}
{{/show_total}}