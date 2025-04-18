<?php
/**
 * Cleanup functions for the Custom Theme.
 *
 * This file contains functions to remove unnecessary WordPress features,
 * scripts, styles, and APIs for improved performance and security.
 *
 * Functions included:
 * - remove_wp_block_library_css(): Remove Gutenberg block library CSS.
 * - disable_emojis(): Disable WordPress emoji scripts and styles.
 * - remove_wp_version(): Remove WordPress version meta tag.
 * - deregister_scripts(): Deregister default WordPress scripts.
 * - clean_up_wp_head(): Clean up extra tags from wp_head().
 * - disable_theme_support(): Disable built-in theme support features.
 * - enqueue_minimal_assets(): Enqueue minimal theme assets.
 * - remove_dashboard_widgets(): Remove default dashboard widgets.
 * - disable_rest_api(): Restrict REST API access to authenticated users.
 * - unregister_default_widgets(): Unregister default WordPress widgets.
 *
 * @package Custom_Theme
 */

/**
 * Remove Gutenberg block library CSS.
 *
 * Dequeues the default block library styles on the frontend to reduce CSS bloat.
 *
 * @return void
 */
function remove_wp_block_library_css() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style'); // For WooCommerce block styles.
}
add_action('wp_enqueue_scripts', 'remove_wp_block_library_css', 100);

/**
 * Disable WordPress emoji scripts and styles.
 *
 * Removes all actions and filters related to emojis to prevent loading extra scripts and styles.
 *
 * @return void
 */
function disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
}
add_action('init', 'disable_emojis');

/**
 * Remove WordPress version meta tag.
 *
 * Removes the generator meta tag from the site's header for security by obscurity.
 *
 * @return void
 */
function remove_wp_version() {
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'remove_wp_version');

// add_filter('show_admin_bar', '__return_false');

/**
 * Deregister default WordPress scripts.
 *
 * Removes bundled scripts such as jQuery. Use this to load custom scripts instead.
 *
 * @return void
 */
function deregister_scripts() {
    wp_deregister_script('jquery'); // Removes bundled jQuery. Add your own if needed.
}
add_action('wp_enqueue_scripts', 'deregister_scripts');

/**
 * Clean up extra tags from wp_head().
 *
 * Removes RSD, WLW manifest links, shortlinks, REST API links, oEmbed discovery links, and canonical link.
 *
 * @return void
 */
function clean_up_wp_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'rel_canonical');
}
add_action('init', 'clean_up_wp_head');

/**
 * Disable built-in theme support features.
 *
 * Removes support for automatic feed links, post formats, custom header, custom background, and core block patterns.
 *
 * @return void
 */
function disable_theme_support() {
    remove_theme_support('automatic-feed-links');
    remove_theme_support('post-formats');
    remove_theme_support('custom-header');
    remove_theme_support('custom-background');
    remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', 'disable_theme_support');

/**
 * Enqueue minimal theme assets.
 *
 * Loads the main stylesheet and primary JavaScript file for the theme.
 *
 * @return void
 */
function enqueue_minimal_assets() {
    wp_enqueue_style('theme-style', get_stylesheet_uri());
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/main.js', [], false, true);
}
add_action('wp_enqueue_scripts', 'enqueue_minimal_assets');

/**
 * Remove default dashboard widgets.
 *
 * Removes Quick Draft, Recent Drafts, Primary and Secondary widgets from the WordPress dashboard.
 *
 * @return void
 */
function remove_dashboard_widgets() {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

/**
 * Restrict REST API access to authenticated users.
 *
 * Returns a WP_Error for unauthenticated REST API requests to lock down public endpoints.
 *
 * @return void
 */
function disable_rest_api() {
    add_filter('rest_authentication_errors', function($result) {
        if (!is_user_logged_in()) {
            return new WP_Error('rest_forbidden', 'REST API restricted.', ['status' => 401]);
        }
        return $result;
    });
}
add_action('init', 'disable_rest_api');

/**
 * Unregister default WordPress widgets.
 *
 * Removes core widgets such as Pages, Calendar, Archives, Meta, Search, Text, Categories, Recent Posts, and Recent Comments.
 *
 * @return void
 */
function unregister_default_widgets() {
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Text');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
}
add_action('widgets_init', 'unregister_default_widgets');