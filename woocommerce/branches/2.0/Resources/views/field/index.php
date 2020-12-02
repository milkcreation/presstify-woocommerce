<?php
/**
 * @var tiFy\Contracts\View\PlatesFactory $this
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <?php echo $this->get('label'); ?><span class="woocommerce-input-wrapper"><?php echo $this->get('field'); ?></span>
</div>