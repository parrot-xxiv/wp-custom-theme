<?php

/**
 * Cleanup functions for the Custom Theme.
 *
 * This file contains functions to remove unnecessary WordPress features,
 * scripts, styles, admin menus, post types, and APIs
 * for improved performance, security, and a streamlined admin experience.
 *
 * Functions included:
 * - remove_wp_block_library_css(): Remove Gutenberg block library CSS.
 * - disable_emojis(): Disable WordPress emoji scripts and styles.
 * - remove_wp_version(): Remove WordPress version meta tag.
 * - clean_up_wp_head(): Clean up extra tags from wp_head().
 * - disable_theme_support(): Disable built-in theme support features.
 * - remove_dashboard_widgets(): Remove default dashboard widgets.
 * - disable_rest_api(): Restrict REST API access to authenticated users.
 * - unregister_default_widgets(): Unregister default WordPress widgets.
 * - remove_appearance_submenus(): Remove Customize, Widgets, and Patterns under Appearance.
 * - disable_default_post_type_post(): Unregister default 'post' post type.
 * - remove_posts_menu_page(): Remove Posts menu from the admin sidebar.
 * - disable_all_comments(): Close comments and pings, hide existing comments, remove admin bar comment link.
 * - remove_comments_post_type_support(): Remove comments & trackbacks support from all post types.
 * - remove_comments_menu_page(): Remove Comments menu page from admin.
 * - disable_comments_rest_endpoints(): Disable comment-related REST API endpoints.
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
function remove_wp_block_library_css()
{
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
function disable_emojis()
{
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
function remove_wp_version()
{
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'remove_wp_version');

// add_filter('show_admin_bar', '__return_false');

/**
 * Clean up extra tags from wp_head().
 *
 * Removes RSD, WLW manifest links, shortlinks, REST API links, oEmbed discovery links, and canonical link.
 *
 * @return void
 */
function clean_up_wp_head()
{
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
function disable_theme_support()
{
    remove_theme_support('automatic-feed-links');
    remove_theme_support('post-formats');
    remove_theme_support('custom-header');
    remove_theme_support('custom-background');
    remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', 'disable_theme_support');

/**
 * Remove default dashboard widgets.
 *
 * Removes Quick Draft, Recent Drafts, Primary and Secondary widgets from the WordPress dashboard.
 *
 * @return void
 */
function remove_dashboard_widgets()
{
    // Remove Quick Draft, Recent Drafts, Primary and Secondary widgets
    remove_meta_box('dashboard_quick_press',   'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_primary',       'dashboard', 'side');
    remove_meta_box('dashboard_secondary',     'dashboard', 'side');
    // Remove At a Glance (Right Now) and Activity panels
    remove_meta_box('dashboard_right_now',     'dashboard', 'normal');
    remove_meta_box('dashboard_activity',      'dashboard', 'normal');
    // Disable the Welcome panel
    remove_action('welcome_panel', 'wp_welcome_panel');
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

/**
 * Restrict REST API access to authenticated users.
 *
 * Returns a WP_Error for unauthenticated REST API requests to lock down public endpoints.
 *
 * @return void
 */
function disable_rest_api()
{
    add_filter('rest_authentication_errors', function ($result) {
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
function unregister_default_widgets()
{
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
/**
 * Remove certain Appearance submenu items: Customize, Widgets, Patterns.
 *
 * @return void
 */
function remove_appearance_submenus()
{
    global $submenu;
    // Only proceed if Appearance menu exists
    if (empty($submenu['themes.php'])) {
        return;
    }
    $unwanted = array(
        __('Customize'),
        __('Widgets'),
        __('Patterns'),
    );
    foreach ($submenu['themes.php'] as $key => $item) {
        if (in_array($item[0], $unwanted, true)) {
            unset($submenu['themes.php'][$key]);
        }
    }
}
add_action('admin_menu', 'remove_appearance_submenus', 999);
/**
 * Disable the default "post" post type.
 *
 * Unregisters the built-in "post" type and removes its admin menu.
 *
 * @return void
 */
function disable_default_post_type_post()
{
    unregister_post_type('post');
    remove_post_type_support('page', 'editor');
}
add_action('init', 'disable_default_post_type_post', 11);

/**
 * Remove Posts menu page.
 *
 * @return void
 */
function remove_posts_menu_page()
{
    remove_menu_page('edit.php');
}
add_action('admin_menu', 'remove_posts_menu_page', 999);
/**
 * Disable comments site-wide.
 *
 * Closes comments and pings, hides existing comments,
 * removes comment links from admin bar,
 * and disables comment-related REST API endpoints.
 *
 * @return void
 */
function disable_all_comments()
{
    // Close comments and pings
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);
    // Remove comments link from admin bar
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
}
add_action('init', 'disable_all_comments', 100);

/**
 * Remove comment support from all post types.
 *
 * @return void
 */
function remove_comments_post_type_support()
{
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'remove_comments_post_type_support', 100);

/**
 * Remove Comments menu page from admin.
 *
 * @return void
 */
function remove_comments_menu_page()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'remove_comments_menu_page', 999);

/**
 * Disable comment REST API endpoints.
 *
 * @param array $endpoints REST API endpoints.
 * @return array
 */
function disable_comments_rest_endpoints($endpoints)
{
    if (isset($endpoints['/wp/v2/comments'])) {
        unset($endpoints['/wp/v2/comments']);
    }
    if (isset($endpoints['/wp/v2/comments/(?P<id>[\\d]+)'])) {
        unset($endpoints['/wp/v2/comments/(?P<id>[\\d]+)']);
    }
    return $endpoints;
}
add_filter('rest_endpoints', 'disable_comments_rest_endpoints', 10, 1);
/**
 * Disable Gutenberg block editor globally.
 *
 * Reverts to classic editor and disables block widgets editor.
 *
 * @return void
 */
function disable_gutenberg_editor()
{
    // Disable block editor for all post types
    add_filter('use_block_editor_for_post_type', '__return_false', 100);
    add_filter('use_block_editor_for_post',      '__return_false', 100);
    // Disable block-based widgets editor
    add_filter('use_widgets_block_editor',       '__return_false');
    // Disable WSIWYG 
    remove_post_type_support('page', 'editor');
}
add_action('init', 'disable_gutenberg_editor', 100);

/**
 * Remove unwanted meta boxes from the page edit screen.
 *
 * Disables Revisions, Custom Fields, Slug, Author, Page Attributes, and Featured Image.
 *
 * @param string $post_type The post type of the current screen.
 */
function remove_page_editor_meta_boxes($post_type)
{
    if ('page' !== $post_type) {
        return;
    }
    remove_meta_box('revisionsdiv',   'page', 'normal');
    remove_meta_box('postcustom',     'page', 'normal');
    remove_meta_box('slugdiv',        'page', 'normal');
    remove_meta_box('authordiv',      'page', 'normal');
    remove_meta_box('pageparentdiv',  'page', 'side');
    remove_meta_box('postimagediv',   'page', 'side');
}
add_action('add_meta_boxes', 'remove_page_editor_meta_boxes', 99);

// Add custom template selector metabox
function custom_add_template_metabox()
{
    add_meta_box(
        'custom_template_selector',
        'Page Template',
        'custom_template_selector_callback',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'custom_add_template_metabox');

// Callback to display template dropdown
function custom_template_selector_callback($post)
{
    // Get current template
    $current_template = get_post_meta($post->ID, '_wp_page_template', true);

    // Get template options
    $templates = get_page_templates($post->ID, 'page');
    ksort($templates); // Sort templates alphabetically

    // Default template option
    echo '<p><label for="page_template">Template:</label></p>';
    echo '<select name="page_template" id="page_template">';
    echo '<option value="default">Default Template</option>';

    // List all available templates
    foreach ($templates as $template_name => $template_file) {
        $selected = ($current_template == $template_file) ? ' selected="selected"' : '';
        echo '<option value="' . esc_attr($template_file) . '"' . $selected . '>' . esc_html($template_name) . '</option>';
    }
    echo '</select>';
}