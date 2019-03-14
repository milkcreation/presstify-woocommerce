<?php
/**
 * ????
 * ---------------------------------------------------------------------------------------------------------------------
 * @var App\Views\ViewController $this
 * @var \WP_Term $term
 * @var array $selected
 */
?>
<li>
    <label>
        <?php
        echo field(
            'radio',
            [
                'name' => '_main_post_term',
                'value' => $term->term_id,
                'checked' => in_array($term->term_id, $selected),
                'after' => $term->name
            ]
        )
        ?>
    </label>
