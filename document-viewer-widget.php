<?php

/*
 * Plugin Name: Document Viewer Widget for Elementor
 * Plugin URI: https://github.com/kamalahmed/document-viewer-widget
 * Description: A custom Elementor widget to upload and display PDF, Markdown, DOCX, and Excel files.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Kamal Ahmed
 * Author URI: http://github.com/kamalahmed
 * License: GPLv3 or later
 * Text Domain: document-viewer-widget
 * Requires Plugins: elementor
 * Elementor tested up to: 3.21.0
 * Elementor Pro tested up to: 3.21.0
*/


/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2024 KamalAhmed.
*/

// Make sure we don't expose any info if called directly
defined( 'ABSPATH' ) || die( 'Direct Access is not allowed!' );

final class Document_Viewer_Widget {
	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var Document_Viewer_Widget The single instance of the class.
	 */
	private static $_instance = null;
	const DV_VERSION = '1.0.0';
	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.21.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( $this->is_compatible() ) {

			// Run init process here...
			$this->init();
		}

	}
	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return Document_Viewer_Widget An instance of the class.
	 */
	public static function instance(): ?Document_Viewer_Widget {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		$this->define_constants();
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts_and_styles' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}


	protected function define_constants() {
		define( 'DV_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
		define( 'DV_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	public function register_widgets( $widgets_manager ) {
		require_once( DV_PLUGIN_DIR_PATH . 'widgets/document-viewer-widget.php' );
		$widgets_manager->register( new DV_Document_Viewer_Widget() );
	}

	public function register_scripts_and_styles() {
		// Scripts
		wp_register_script( 'dv-pdfobject', DV_PLUGIN_DIR_URL . 'assets/js/pdfobject.min.js', [], '2.2.7', true );
		wp_register_script( 'dv-marked', DV_PLUGIN_DIR_URL . 'assets/js/marked.min.js', [], '12.0.2', true );
		wp_register_script( 'dv-mammoth', DV_PLUGIN_DIR_URL . 'assets/js/mammoth.browser.min.js', [], '1.4.2', true );
		wp_register_script( 'dv-xlsx', DV_PLUGIN_DIR_URL . 'assets/js/xlsx.full.min.js', [], '0.17.0', true );
		// Styles
		wp_register_style( 'dv-style', DV_PLUGIN_DIR_URL . 'assets/css/style.css', [], self::DV_VERSION );
	}


	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible(): bool {

		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin(): void {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'document-viewer-widget' ),
			'<strong>' . esc_html__( 'Document Viewer Widget', 'document-viewer-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'document-viewer-widget' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version(): void {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'document-viewer-widget' ),
			'<strong>' . esc_html__( 'Document Viewer Widget', 'document-viewer-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'document-viewer-widget' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version(): void {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'document-viewer-widget' ),
			'<strong>' . esc_html__( 'Document Viewer Widget', 'document-viewer-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'document-viewer-widget' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

}


// run the plugin after elementor has loaded.
function document_viewer_init() {

	Document_Viewer_Widget::instance();

}
add_action( 'plugins_loaded', 'document_viewer_init' );