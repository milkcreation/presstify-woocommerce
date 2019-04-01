<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var string $for Attribut "for" du label.
 * @var string $class Classes du label.
 * @var string $content Contenu du label.
 * @var bool $required Champ requis.
 */
?>
<label for="<?php echo esc_attr($for); ?>" class="<?php echo esc_attr($class); ?>">
    <?php
    echo $content;
    if ($required) :
        $this->insert('field/required');
    endif;
    ?>
</label>
