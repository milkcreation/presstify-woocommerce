<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var array $args Arguments du champ.
 * @var string $key Nom du champ.
 * @var mixed $value Valeur du champ.
 */
?>
<input type="text" class="input-text <?php echo esc_attr(implode(' ', $args['input_class'])); ?>"
       value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($args['placeholder']); ?>"
       name="<?php echo esc_attr($key); ?>"
       id="<?php echo esc_attr($args['id']); ?>" <?php echo $args['custom_attributes']; ?>>