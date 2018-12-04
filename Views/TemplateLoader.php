<?php

namespace tiFy\Plugins\Woocommerce;

use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Core\Router\Router;

class TemplateLoader
{
    /**
     * @var ViewsInterface
     */
    protected $views;

    /**
     * CONSTRUCTEUR.
     *
     * @param AppInterface $app
     * @param ViewsInterface $views
     *
     */
    public function __construct($app, $views = null)
    {
        $this->views = $views instanceof ViewsInterface ? $views : $app->appTemplates();

        add_action(
            'init',
            function() {
                $this->views->addFolder('wctheme', get_stylesheet_directory() . DIRECTORY_SEPARATOR . \WC()->template_path());
                $this->views->addFolder('wcplugin', \WC()->plugin_path() . '/templates' . DIRECTORY_SEPARATOR);
            }
        );

        add_filter('template_include', [$this, 'template_include'], 99);

        add_filter('wc_get_template', [$this, 'wc_get_template'], 10, 5);

        add_filter('wc_get_template_part', [$this, 'wc_get_template_part'], 10, 3);
    }

    /**
     * Chargement d'un template WooCommerce.
     *
     * @param string $template Chemin absolu du fichier de template à charger.
     * @param array $args Attributs du template.
     *
     * @return void
     */
    public function loadWooTemplate($template = '', $args = [])
    {
        if (preg_match('#' . preg_quote(get_stylesheet_directory(), DIRECTORY_SEPARATOR) . '#', $template)) :
            $folder = 'wctheme';
            $directory = $this->views->getFolders()->get($folder)->getPath();
        else :
            $folder = 'wcplugin';
            $directory = $this->views->getFolders()->get($folder)->getPath();
        endif;

        $patterns = $replacements = [];
        $patterns[] = '#' . preg_quote($directory, DIRECTORY_SEPARATOR) . '#';
        $patterns[] = '#\.php?$#';
        $replacements[] = '';
        $path = preg_replace($patterns, $replacements, $template);

        $name = "{$folder}::{$path}";

        echo $this->views->render($name, $args);
    }

    /**
     * Court-cicuitage du chemin vers le gabarit d'affichage.
     *
     * @param string $template Chemin vers le gabarit d'affichage.
     *
     * @return string
     */
    public function template_include($template)
    {
        if (is_woocommerce() || is_account_page() || is_cart() || is_checkout() || apply_filters('tify_woocommerce_use_wc_templates', false)) :
            if (preg_match('#' . preg_quote(get_stylesheet_directory(), DIRECTORY_SEPARATOR) . '#', $template)) :
                $folder = 'wctheme';
                $directory = $this->views->getFolders()->get($folder)->getPath();
            else :
                $folder = 'wcplugin';
                $directory = $this->views->getFolders()->get($folder)->getPath();
            endif;

            $template = preg_replace(
                '#' . preg_quote($directory, DIRECTORY_SEPARATOR) . '#',
                '',
                $template
            );

            $template = "{$folder}::{$template}";
        endif;

        return $template;
    }

    /**
     * Surcharge du moteur de template WooCommerce.
     *
     * @param string $located Chemin absolu du fichier de template à charger.
     * @param string $template_name Nom du template.
     * @param array $args Attributs du template.
     * @param string $template_path
     * @param string $default_path
     *
     * @return string
     */
    public function wc_get_template($located, $template_name, $args, $template_path, $default_path)
    {
        if (is_admin() && !wp_doing_ajax()) :
            return $located;
        endif;

        $this->loadWooTemplate($located, $args);

        return __DIR__ . '/index.php';
    }

    /**
     * Surcharge du moteur de template (partiel) WooCommerce.
     *
     * @param string $template Chemin absolu du fichier de template partiel à charger.
     * @param string $slug Identifiant du fichier de template partiel.
     * @param string $name Nom du fichier de template partiel.
     *
     * @return bool
     */
    public function wc_get_template_part($template, $slug, $name)
    {
        $this->loadWooTemplate($template);

        return $template = false;
    }
}