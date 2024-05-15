<?php

/*
Plugin Name: Document Viewer Widget for Elementor
Plugin URI: https://github.com/kamalahmed/document-viewer-widget
Description: A custom Elementor widget to upload and display PDF, Markdown, DOCX, and Excel files.
Version: 1.0.0
Author: Kamal Ahmed
Author URI: http://github.com/kamalahmed
License: GPLv3 or later
Text Domain: document-viewer-widget
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
defined('ABSPATH') || die('Direct Access is not allowed!');

const DV_VERSION = '1.0.0';

define( 'DV_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'DV_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

// Load the Elementor widget
function register_document_viewer_widget($widgets_manager) {
	require_once(DV_PLUGIN_DIR_PATH . 'widgets/document-viewer-widget.php');
	$widgets_manager->register_widget_type(new DV_Document_Viewer_Widget());

}
add_action('elementor/widgets/widgets_registered', 'register_document_viewer_widget');
// Enqueue necessary scripts and styles
add_action('wp_enqueue_scripts', 'document_viewer_widget_enqueue_scripts');
add_action('elementor/editor/after_enqueue_scripts', 'document_viewer_widget_enqueue_scripts');
add_action('elementor/editor/after_enqueue_styles', 'document_viewer_widget_enqueue_styles');
function document_viewer_widget_enqueue_scripts() {

	wp_register_script('dv-pdfobject', DV_PLUGIN_DIR_URL .'assets/js/pdfobject.min.js', array(), '2.2.7', true);
	wp_register_script('dv-marked', DV_PLUGIN_DIR_URL .'assets/js/marked.min.js', array(), '12.0.2', true);
	wp_register_script('dv-mammoth', DV_PLUGIN_DIR_URL .'assets/js/mammoth.browser.min.js', array(), '1.4.2', true);
	wp_register_script('dv-xlsx', DV_PLUGIN_DIR_URL .'assets/js/xlsx.full.min.js', array(), '0.17.0', true);

	wp_enqueue_script('dv-pdfobject');
	wp_enqueue_script('dv-marked');
	wp_enqueue_script('dv-mammoth');
	wp_enqueue_script('dv-xlsx');

}
// Editor scripts

function document_viewer_widget_enqueue_styles() {
	wp_register_style('dv-style', DV_PLUGIN_DIR_URL .'assets/css/style.css', array(), DV_VERSION);
	wp_enqueue_style('dv-style');
}

