<?php
// First, load Composer's autoloader
require_once dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';

// Set WP_TESTS_DIR to point to your existing test environment
$_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : '/Users/kamalahmed/wp-tests/wordpress-develop/tests/phpunit';

// Ensure the WordPress test suite exists
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
    echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
    exit( 1 );
}

// Manually load the plugin being tested
function _manually_load_plugin() {
    require dirname( dirname( __FILE__ ) ) . '/document-viewer-widget.php';
}

// Load the WordPress tests functions
require_once $_tests_dir . '/includes/functions.php';

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';