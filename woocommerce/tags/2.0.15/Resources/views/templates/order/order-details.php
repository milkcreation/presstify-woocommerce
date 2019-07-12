<?php
/**
 * Woocommerce - Mon Compte - Détails commande
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.3.0
 *
 * @var App\Views\ViewController $this
 * @var WC_Order $order
 * @var WC_Order_Item_Product[] $order_items
 * @var boolean $show_purchase_note
 * @var boolean $show_customer_details
 * @var array $downloads
 * @var boolean $show_downloads
 * @var WC_Product $product
 */
?>

<?php $this->layout('wc.myaccount::my-account'); ?>

<section class="MyAccountOrderDetails">

    <h2 class="Woocommerce-title"><?php _e('Order details', 'woocommerce'); ?></h2>
      
    <table class="WcTable WcTableOrderDetails">

        <thead class="WcTable-head">
        <tr class="WcTable-row WcTable-row--head">
            <th class="WcTable-cell WcTable-cell--head WcTable-cell--productName">
                <?php _e('Product', 'woocommerce'); ?>
            </th>
            <th class="WcTable-cell WcTable-cell--head WcTable-cell--productTotal">
                <?php _e('Total', 'woocommerce'); ?>
            </th>
        </tr>
        </thead>

        <tbody class="WcTable-body">
        <?php
        do_action('woocommerce_order_details_before_order_table_items', $order);

        foreach ($order_items as $item_id => $item) :
            $product = $item->get_product();
            $purchase_note = $product ? $product->get_purchase_note() : '';

            $this->insert(
                'wc.order::order-details-item',
                compact(
                    'order',
                    'item_id',
                    'item',
                    'show_purchase_note',
                    'purchase_note',
                    'product'
                )
            );
        endforeach;

        do_action('woocommerce_order_details_after_order_table_items', $order);
        ?>
        </tbody>

        <tfoot class="WcTable-footer">

        <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
            <tr class="WcTable-row WcTable-row--footer WcTable-row--price">
                <th scope="row" class="WcTable-cell WcTable-cell--footer WcTable-cell--label">
                    <?php echo $total['label']; ?>
                </th>
                <td class="WcTable-cell WcTable-cell--footer WcTable-cell--value">
                    <?php echo $total['value']; ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if ($order->get_customer_note()) : ?>
            <tr class="WcTable-row WcTable-row--footer WcTable-row--note">
                <th class="WcTable-cell WcTable-cell--footer WcTable-cell--label">
                    <?php _e('Note:', 'woocommerce'); ?>
                </th>
                <td class="WcTable-cell WcTable-cell--footer WcTable-cell--value">
                    <?php echo wptexturize($order->get_customer_note()); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tfoot>
    </table>

    <?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</section>

<?php
if ( $show_customer_details ) :
    $this->insert('wc.order::order-details-customer', compact('order'));
endif;