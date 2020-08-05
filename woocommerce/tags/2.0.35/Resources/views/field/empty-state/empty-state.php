<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var array $args Arguments du champ.
 * @var string $key Nom du champ.
 */
?>
<input type="hidden" class="hidden" name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($args['id']); ?>"
       value="" <?php echo $args['custom_attributes']; ?> placeholder="<?php echo esc_attr($args['placeholder']); ?>"
       readonly="readonly">