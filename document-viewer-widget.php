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
	error_log('did it run successfully');
	require_once(DV_PLUGIN_DIR_PATH . 'widgets/document-viewer-widget.php');
	$widgets_manager->register_widget_type(new DV_Document_Viewer_Widget());

}
add_action('elementor/widgets/widgets_registered', 'register_document_viewer_widget');
// Enqueue necessary scripts and styles
function document_viewer_widget_scripts() {
	wp_enqueue_script('pdfobject', 'https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.7/pdfobject.min.js', array(), null, true);
	wp_enqueue_script('marked', 'https://cdn.jsdelivr.net/npm/marked/marked.min.js', array(), null, true);
	wp_enqueue_script('mammoth', 'https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js', array(), null, true);
	wp_enqueue_script('xlsx', 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'document_viewer_widget_scripts');

