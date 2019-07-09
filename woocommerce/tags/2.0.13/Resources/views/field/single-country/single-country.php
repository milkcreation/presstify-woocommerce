<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var array $args Arguments du champ.
 * @var string $key Nom du champ.
 */
?>
<strong><?php echo current(array_values($args['choices'])); ?></strong>
<input type="hidden" name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($args['id']); ?>"
       value="<?php echo current(array_keys($args['choices'])); ?>" <?php echo $args['custom_attributes']; ?>
       class="country_to_state" readonly="readonly">