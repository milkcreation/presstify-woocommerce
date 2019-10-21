<?php
/**
 * Woocommerce - Mon Compte - Commandes
 * ---------------------------------------------------------------------------------------------------------------------
 * @version 3.2.0
 *
 * @var App\Views\ViewController $this
 * @var int $current_page
 * @var WC_Order[]|stdClass $orders
 * @var array $columns
 * @var boolean $has_orders
 * @var float $max_num_pages
 */
?>

<?php $this->layout('wc.myaccount::my-account'); ?>

<section class="MyAccountOrders">
    <?php if ($has_orders) : ?>
        <table class="WcTable WcTableOrders">
            <thead class="WcTable-head">
            <tr class="WcTable-row WcTable-row--head">
                <?php foreach ($columns as $column_id => $column_name) : ?>
                    <th class="WcTable-cell WcTable-cell--head WcTable-cell--<?php echo esc_attr($column_id); ?>">
                        <?php echo esc_html($column_name); ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>

            <tbody class="WcTable-body">
            <?php foreach ($orders as $order) : $item_count = $order->get_item_count(); ?>
                <tr class="WcTable-row WcTable-row--body WcTable-row--<?php echo esc_attr($order->get_status()); ?>">
                    <?php foreach ($columns as $column_id => $column_name) : ?>
                        <td class="WcTable-cell WcTable-cell--body WcTable-cell--<?php echo esc_attr($column_id); ?>"
                            data-title="<?php echo esc_attr($column_name); ?>">
                            <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
                                <?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

                            <?php elseif ('order-number' === $column_id) : ?>
                                <a href="<?php echo route('wc.myaccount.orders', [$order->get_id()]); ?>">
                                    <?php
                                    echo _x('#', 'hash before order number', 'woocommerce') . $order->get_order_number();
                                    ?>
                                </a>

                            <?php elseif ('order-date' === $column_id) : ?>
                                <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>">
                                    <?php echo esc_html(wc_format_datetime($order->get_date_created(), 'd.m.y')); ?>
                                </time>

                            <?php elseif ('order-status' === $column_id) : ?>
                                <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>

                            <?php elseif ('order-total' === $column_id) : ?>
                                <?php
                                printf(
                                    _n('%1$s', '%1$s', $item_count, 'woocommerce'),
                                    $order->get_formatted_order_total(),
                                    $item_count
                                );
                                ?>

                            <?php elseif ('order-actions' === $column_id) : ?>
                                <?php
                                $actions = wc_get_account_orders_actions($order);

                                if (!empty($actions)) {
                                    foreach ($actions as $key => $action) {
                                        echo '<a href="' . esc_url($action['url']) . '" 
                                        class="WcTable-button ' . sanitize_html_class($key) . '">
                                        ' . esc_html($action['name']) . '
                                        </a>';
                                    }
                                }
                                ?>

                            <?php elseif ('order-again' === $column_id) : ?>
                                <a href="#" class="WcTable-link WcTable-link--orderAgain"
                                   title="<?php _e('Commander cette commande', 'tify'); ?>">
                                    <?php
                                    _e('Commander à nouveau', 'tify');
                                    ?>
                                </a>

                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (1 < $max_num_pages) : ?>
            <div class="WcPagination clearfix">
                <?php if (1 !== $current_page) : ?>
                    <a class="WcPagination-button WcPagination-button--prev Readmore Readmore--1"
                       href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">
                        <span class="Readmore-inner Readmore-inner--1">
                            <?php _e('Précédent', 'tify'); ?>
                        </span>
                    </a>
                <?php endif; ?>

                <?php if (intval($max_num_pages) !== $current_page) : ?>
                    <a class="WcPagination-button WcPagination-button--next Readmore Readmore--1"
                       href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">
                        <span class="Readmore-inner Readmore-inner--1">
                            <?php _e('Suivant', 'tify'); ?>
                        </span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="WcNotice WcNotice--noOrder text-center">
            <a class="WcNotice-button Readmore Readmore--1"
               href="<?php
               echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop')));
               ?>">
                <span class="Readmore-inner Readmore-inner--1">
                    <?php _e('Go shop', 'woocommerce') ?>
                </span>
            </a>
            <div class="WcNotice-text">
                <?php _e('No order has been made yet.', 'woocommerce'); ?>
            </div>
        </div>
    <?php endif; ?>
</section>
