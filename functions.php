<?php
/**
 * Custom Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Custom_Theme
 */

if ( ! defined( 'CUSTOM_THEME_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'CUSTOM_THEME_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the afterCUSTOM_THEMEetup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function custom_theme_setup() {

	load_theme_textdomain( 'custom-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'custom-theme' ),
		)
	);

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'custom_theme_setup' );


/**
 * Enqueue scripts and styles.
 */
function custom_theme_scripts() {
	wp_enqueue_style( 'main', get_stylesheet_uri(), array(), CUSTOM_THEME_VERSION );
	wp_enqueue_style( 'tailwind', get_template_directory_uri() . "/css/style.css", array(), CUSTOM_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_scripts' );

/**
 * require_once instead of require.
 */

require_once get_template_directory() . '/inc/cleanup.php';
