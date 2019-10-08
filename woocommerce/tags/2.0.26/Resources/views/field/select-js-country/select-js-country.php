<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var array $args Arguments du champ.
 * @var string $key Nom du champ.
 * @var mixed $value Valeur du champ
 */
?>
<?php $this->insert('field/select-js/select-js', compact('args', 'key', 'value')); ?>
<noscript>
    <button type="submit" name="woocommerce_checkout_update_totals"
            value="<?php echo esc_attr__('Update country', 'woocommerce'); ?>"><?php echo esc_html__('Update country', 'woocommerce'); ?></button>
</noscript>