<?php

namespace tiFy\Plugins\Woocommerce\Routing\ProductCat;

use League\Uri\Components\HierarchicalPath;
use tiFy\Metabox\MetaboxWpPostController;
use tiFy\Metabox\MetaboxView;

class ProductCat extends MetaboxWpPostController
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action('wp_ajax_theme_metabox_item_change_permalink', [$this, 'wp_ajax']);
    }

    /**
     * {@inheritdoc}
     */
    public function content($post = null, $args = null, $null = null)
    {
        if (empty($args['taxonomy'])) :
            return '';
        endif;

        /** @var \WP_Taxonomy $taxonomy */
        $taxonomy = get_taxonomy($args['taxonomy']);
        $terms = get_terms(['taxonomy' => $taxonomy->name, 'get' => 'all']);
        $selected = absint(get_post_meta($post->ID, '_main_post_term', true));
        $radioWalker = new RadioWalker($this->viewer());
        $route_name = !empty($args['route_name']) ? $args['route_name'] : '';

        return $this->viewer('metabox', compact('post', 'radioWalker', 'route_name', 'selected', 'taxonomy', 'terms'));
    }

    /**
     * {@inheritdoc}
     */
    public function header($post = null, $args = null, $null = null)
    {
        return $this->item->getTitle() ?: __('Catégories du produit', 'theme');
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        if ($wp_screen->id === 'product') :
            add_action(
                'admin_enqueue_scripts',
                function () {
                    wp_enqueue_style('ProductCat', class_info($this)->getUrl() . '/Resources/css/metabox.css');
                    wp_enqueue_script('ProductCat', class_info($this)->getUrl() . '/Resources/js/metabox.js');
                    wp_localize_script(
                        'ProductCat',
                        'productDatas',
                        [
                            'product_slug' => basename(get_permalink()),
                            'shop_url'     => route('shop')
                        ]
                    );
                }
            );
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return ['_main_post_term'];
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $cinfo = class_info($this);
            $default_dir = $cinfo->getDirname() . '/Resources/views';
            $this->viewer = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(MetaboxView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname())
                )
                ->set('metabox', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }

    /**
     * Modification du permalien de l'élément.
     *
     * @return void
     */
    public function wp_ajax()
    {
        check_ajax_referer('theme_metabox_item_change_permalink');
        $term_id = request()->post('termId', 0);

        $segments = [];
        foreach (array_reverse(get_ancestors($term_id, request()->post('taxName'), 'taxonomy')) as $ancestor_id) :
            $segments[] = sanitize_title(get_term($ancestor_id)->name);
        endforeach;
        $segments[] = sanitize_title(get_term($term_id)->name);
        $segments[] = sanitize_title(request()->post('postTitle'));

        $relative_path = HierarchicalPath::createFromSegments($segments)->getContent();
        $post_link = ($route_name = request()->post('routeName')) ? route($route_name, [$relative_path]) : env('SITE_URL') . '/' . $relative_path;

        wp_send_json($post_link);
    }
}