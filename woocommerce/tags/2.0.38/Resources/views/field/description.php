<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var string $id Identifiant de la description.
 * @var string $description Contenu de la description.
 */
?>
<span class="description" id="<?php echo esc_attr($id); ?>-description"
      aria-hidden="true"><?php echo $description; ?></span>