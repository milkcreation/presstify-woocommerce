<?php
/**
 * Woccommerce - Mon Compte - Détails d'une ligne de commande.
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.0.0
 *
 * @var App\Views\ViewController $this
 * @var WC_Order $order
 * @var int $item_id
 * @var WC_Order_Item_Product $item
 * @var boolean $show_purchase_note
 * @var string $purchase_note
 * @var WC_Product_Simple $product
 */
?>

<tr class="WcTable-row WcTable-row--body">
    <td class="WcTable-cell WcTable-cell--body WcTable-cell--productName">
        <?php
        $is_visible = $product && $product->is_visible();
        $product_permalink = apply_filters(
            'woocommerce_order_item_permalink',
            $is_visible ? $product->get_permalink($item) : '',
            $item,
            $order
        );

        echo apply_filters(
            'woocommerce_order_item_name',
            $product_permalink ?
                sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name())
                : $item->get_name(),
            $item,
            $is_visible
        );

        echo apply_filters(
            'woocommerce_order_item_quantity_html',
            ' <strong class="product-quantity">' . sprintf('&times; %s', $item->get_quantity()) . '</strong>',
            $item
        );

        wc_display_item_meta($item);

        ?>
    </td>

    <td class="WcTable-cell WcTable-cell--body WcTable-cell--productTotal">
        <?php echo $order->get_formatted_line_subtotal($item); ?>
    </td>

</tr>

<?php if ($show_purchase_note && $purchase_note) : ?>

    <tr class="WcTable-row WcTable-row--body WcTable-row--purchaseNote">
        <td colspan="2" class="WcTable-cell WcTable-cell--body">
            <?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); ?>
        </td>
    </tr>

<?php endif; ?>
