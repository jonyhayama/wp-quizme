<?php
/*
Plugin Name: WP Quiz Me
Plugin URI: 
Description: WP Quiz Me
Version: 0.0.2
Author: Jony Hayama
Author URI: https://jony.dev
*/

define( 'WP_QUIZME_PLUGIN_FILE', __FILE__ );
define( 'WP_QUIZME_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_QUIZME_PLUGIN_URL', plugins_url('', __FILE__ ) );
define( 'WP_QUIZME_ASSETS_URL', WP_QUIZME_PLUGIN_URL . '/app/assets' );
define( 'WP_QUIZME_APP_PATH', WP_QUIZME_DIR_PATH . 'app' . DIRECTORY_SEPARATOR );
define( 'WP_QUIZME_ASSETS_PATH', WP_QUIZME_DIR_PATH . '/app/assets' );

require_once( WP_QUIZME_APP_PATH . 'application.class.php' );

function wp_quizme( $module = '' ){
	static $application = null;
	if( !$application ){
		$application = new wpQuizme();
	} 
	if( $module ){
		return $application->getModule( $module );
	}
	return $application;
}
wp_quizme();


require 'lib/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonyhayama/wp-quizme',
	__FILE__,
	'wp-quizme'
);

//Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('production');