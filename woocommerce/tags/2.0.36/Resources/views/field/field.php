<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var array $args Arguments du champ.
 * @var string $key Nom du champ.
 * @var mixed $value Valeur du champ
 * @var string $field_type Type de champ.
 */
?>
<div class="form-row <?php echo esc_attr(implode(' ', $args['class'])); ?>"
   id="<?php echo esc_attr($args['id']) . '_field'; ?>"
   data-priority="<?php echo $args['priority'] ? $args['priority'] : ''; ?>" <?php echo $this->get('args.inline_style'); ?>>
    <?php
    $this->insert(
        'field/label',
        [
            'for'      => $args['id'],
            'class'    => implode(' ', $args['label_class']),
            'content'  => $args['label'],
            'required' => $args['required']
        ]
    );
    ?>
    <span class="woocommerce-input-wrapper">
        <?php
        $this->insert("field/{$field_type}/{$field_type}", compact('args', 'key', 'value'));
        if ($args['description']) :
            $this->insert(
                'field/description',
                [
                    'id'          => $args['id'],
                    'description' => wp_kses_post($args['description'])
                ]
            );
        endif;
        ?>
    </span>
</div>