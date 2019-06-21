<?php

namespace tiFy\Plugins\Woocommerce\Views;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\TemplateLoader as TemplateLoaderContract;
use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;
use tiFy\View\ViewEngine;
use WC;

class TemplateLoader extends ParamsBag implements TemplateLoaderContract
{
    use WoocommerceResolverTrait;

    /**
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param AppInterface $app Instance de l'application.
     * @param array $attrs Attributs de configuration.
     *
     * @return void
     */
    public function __construct($app, $attrs = [])
    {
        parent::__construct($attrs);

        $viewer = $this->get('viewer');

        $this->viewer =  $viewer instanceof ViewEngine ? $viewer : $app->viewer();

        add_action(
            'init',
            function() {
                $this->viewer->addFolder('wctheme', get_stylesheet_directory() . DIRECTORY_SEPARATOR . WC()->template_path());
                $this->viewer->addFolder('wcplugin', WC()->plugin_path() . '/templates' . DIRECTORY_SEPARATOR);
            }
        );

        add_filter('template_include', [$this, 'template_include'], 99);

        add_filter('wc_get_template', [$this, 'wc_get_template'], 10, 5);

        add_filter('wc_get_template_part', [$this, 'wc_get_template_part'], 10, 3);
    }

    /**
     * Chargement d'un template Woocommerce.
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
            $directory = $this->viewer->getFolders()->get($folder)->getPath();
        else :
            $folder = 'wcplugin';
            $directory = $this->viewer->getFolders()->get($folder)->getPath();
        endif;

        $patterns = $replacements = [];
        $patterns[] = '#' . preg_quote($directory, DIRECTORY_SEPARATOR) . '#';
        $patterns[] = '#\.php?$#';
        $replacements[] = '';
        $path = preg_replace($patterns, $replacements, $template);

        $name = "{$folder}::{$path}";

        echo $this->viewer->render($name, $args);
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
                $directory = $this->viewer->getFolders()->get($folder)->getPath();
            else :
                $folder = 'wcplugin';
                $directory = $this->viewer->getFolders()->get($folder)->getPath();
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
     * Surcharge du moteur de template Woocommerce.
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

        if (!isset($args['args']) && !empty($args)) :
            $args['args'] = $args;
        endif;

        $this->loadWooTemplate($located, $args);

        return $this->viewer()->getDirectory() . '/index.php';
    }

    /**
     * Surcharge du moteur de template (partiel) Woocommerce.
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