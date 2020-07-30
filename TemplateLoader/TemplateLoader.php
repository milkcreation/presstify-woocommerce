<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\TemplateLoader;

use tiFy\Plugins\Woocommerce\{Contracts\TemplateLoader as TemplateLoaderContract, WoocommerceAwareTrait};
use tiFy\Support\{ParamsBag, Proxy\View};

class TemplateLoader extends ParamsBag implements TemplateLoaderContract
{
    use WoocommerceAwareTrait;

    /**
     * Séparateur de répertoire.
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('init', function () {
            View::addFolder('wctheme', get_stylesheet_directory() . self::DS . WC()->template_path());
            View::addFolder('wcplugin', WC()->plugin_path() . '/templates' . self::DS);
        });

        /**
         * Court-cicuitage du chemin vers le gabarit d'affichage.
         *
         * @param string $template Chemin vers le gabarit d'affichage.
         *
         * @return string
         */
        add_filter('template_include', function (string $template): string {
            if (is_woocommerce() || is_account_page() || is_cart() || is_checkout()) {
                if (preg_match('/' . preg_quote(get_stylesheet_directory(), self::DS) . '/', $template)) {
                    $folder = 'wctheme';
                    $directory = View::getFolder($folder)->getPath();
                } else {
                    $folder = 'wcplugin';
                    $directory = View::getFolder($folder)->getPath();
                }
                $template = "{$folder}::" . preg_replace('/' . preg_quote($directory, self::DS) . '/', '', $template);
            }

            return $template;
        }, 99);

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
        add_filter('wc_get_template', function (
                string $located,
                string $template_name,
                array $args,
                string $template_path,
                string $default_path
            ): string {
                if (is_admin() && !wp_doing_ajax()) {
                    return $located;
                } elseif (!isset($args['args']) && !empty($args)) {
                    $args['args'] = $args;
                }
                $this->loadWcTemplate($located, $args);

                return __DIR__ . '/index.php';
            }, 10, 5);

        /**
         * Surcharge du moteur de template (partiel) Woocommerce.
         *
         * @param string $template Chemin absolu du fichier de template partiel à charger.
         * @param string $slug Identifiant du fichier de template partiel.
         * @param string $name Nom du fichier de template partiel.
         *
         * @return bool
         */
        add_filter('wc_get_template_part', function (string $template, string $slug, string $name): bool
        {
            $this->loadWcTemplate($template);

            return $template = false;
        }, 10, 3);
    }

    /**
     * @inheritDoc
     */
    public function loadWcTemplate(string $template = '', array $args = []): void
    {
        if (preg_match('#' . preg_quote(get_stylesheet_directory(), self::DS) . '#', $template)) {
            $folder = 'wctheme';
            $directory = View::getFolder($folder)->getPath();
        } else {
            $folder = 'wcplugin';
            $directory = View::getFolder($folder)->getPath();
        }

        $patterns = $replacements = [];
        $patterns[] = '#' . preg_quote($directory, self::DS) . '#';
        $patterns[] = '#\.php?$#';
        $replacements[] = '';
        $path = preg_replace($patterns, $replacements, $template);

        $name = "{$folder}::{$path}";

        echo View::render($name, $args);
    }
}