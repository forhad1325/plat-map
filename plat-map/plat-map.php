<?php
/**
 * Plugin Name:       Plat Map
 * Plugin URI:        https://github.com/forhad1325/plat-map/
 * Description:       This is a plugin for property management plat map creation.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            MD. Forhad Hasan
 * Author URI:        https://forhadhasan.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       plat-map
 * Domain Path:       /languages
 * Requires Plugins:  estatik, advanced-custom-fields
 */

if (!defined('ABSPATH')) exit;

class Plat_Map_Plugin {

    public function __construct() {
        add_action('init', [$this, 'register_plat_map_post_type']);
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
        add_action('save_post', [$this, 'save_plat_map_meta']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_shortcode('plat_map', [$this, 'render_plat_map_shortcode']);
    }

    public function register_plat_map_post_type() {
        register_post_type('plat_map', [
            'labels' => [
                'name' => __('Plat Maps', 'plat-map'),
                'singular_name' => __('Plat Map', 'plat-map'),
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-location',
            'supports' => ['title'],
        ]);
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'plat-map-style',
            plugin_dir_url(__FILE__) . 'assets/css/style.css',
            [],
            filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
        );
    
        wp_enqueue_script(
            'plat-map-script',
            plugin_dir_url(__FILE__) . 'assets/js/main.js',
            ['jquery'],
            filemtime(plugin_dir_path(__FILE__) . 'assets/js/main.js'),
            true
        );
    }    

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
        if (get_post_type() !== 'plat_map') return;

        wp_enqueue_style('plat-map-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin.css');
        wp_enqueue_script('plat-map-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['jquery'], null, true);
        wp_localize_script('plat-map-admin-script', 'platMapData', [
            'ajax_url'   => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('plat_map_nonce'),
            'plugin_url' => plugin_dir_url(__FILE__)
        ]);
        
    }

    public function register_meta_boxes() {
        add_meta_box('plat_map_meta_box', 'Plat Map Settings', [$this, 'render_plat_map_metabox'], 'plat_map', 'normal', 'default');
    }

    public function render_plat_map_metabox($post) {
        wp_nonce_field('plat_map_meta_save', 'plat_map_meta_nonce');
        include plugin_dir_path(__FILE__) . 'templates/admin/plat-map-add.php';
    }
    

    private function render_property_coordinates_fields($term_id, $coordinates) {
        include plugin_dir_path(__FILE__) . 'templates/admin/property-coordinates-fields.php';
    }
    

    public function save_plat_map_meta($post_id) {
        if (!isset($_POST['plat_map_meta_nonce']) || !wp_verify_nonce($_POST['plat_map_meta_nonce'], 'plat_map_meta_save')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        update_post_meta($post_id, '_plat_map_term', sanitize_text_field($_POST['plat_map_term']));
        update_post_meta($post_id, '_plat_map_image_id', intval($_POST['plat_map_image_id']));
        update_post_meta($post_id, '_plat_map_coordinates', $_POST['property_coords']);
    }

    public function render_plat_map_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => '',
        ], $atts);

        $post_id = intval($atts['id']);
        if (!$post_id) return '';

        $image_id = get_post_meta($post_id, '_plat_map_image_id', true); // NOTE: Fixed meta key name with underscore
        $image_url = wp_get_attachment_url($image_id);
        if (!$image_url) return '';

        $properties = get_post_meta($post_id, '_plat_map_coordinates', true);
        if (empty($properties) || !is_array($properties)) return '';

        $args = [
            'image_url'   => $image_url,
            'properties'  => $properties,
            'plugin_url'  => plugin_dir_url(__FILE__),
        ];

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/frontend/plat-map-view.php';
        return ob_get_clean();
    }
    
}

new Plat_Map_Plugin();