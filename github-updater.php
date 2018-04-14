<?php
/**
 * GitHub Updater
 *
 * @package   GitHub_Updater
 * @author    Andy Fragen
 * @license   GPL-2.0+
 * @link      https://github.com/afragen/github-updater
 */

/**
 * Plugin Name:       GitHub Updater
 * Plugin URI:        https://github.com/afragen/github-updater
 * Description:       A plugin to automatically update GitHub, Bitbucket, GitLab, or Gitea hosted plugins, themes, and language packs. It also allows for remote installation of plugins or themes into WordPress.
 * Version:           7.6.0
 * Author:            Andy Fragen
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       github-updater
 * Network:           true
 * GitHub Plugin URI: https://github.com/afragen/github-updater
 * GitHub Languages:  https://github.com/afragen/github-updater-translations
 * Requires WP:       4.6
 * Requires PHP:      5.3
 */

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * GHU_DB_VERSION - holds current database version,
 * checked on plugin update when changes are made to
 * the structure of tables
 */
define("GHU_DB_VERSION", "0.0");

/**
 * GHU_TABLE_LOGS - holds current log table name
 */
define("GHU_TABLE_LOGS", "ghu_logs");

/**
 * Useful path constants
 */
define('GHU_PLUGIN_FILE', __FILE__);
define('GHU_PLUGIN_ROOT', dirname(__FILE__) . '/');
define('GHU_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));

if ( version_compare( '5.3.0', PHP_VERSION, '>=' ) ) {
	?>
	<div class="error notice is-dismissible">
		<p>
			<?php printf( esc_html__( 'GitHub Updater cannot run on PHP versions older than %s. Please contact your hosting provider to update your site.', 'github-updater' ), '5.3.0' ); ?>
		</p>
	</div>
	<?php

	return false;
}

// Load textdomain.
load_plugin_textdomain( 'github-updater' );

// Plugin namespace root.
$ghu['root'] = array( 'Fragen\\GitHub_Updater' => __DIR__ . '/src/GitHub_Updater' );

// Add extra classes.
$ghu['extra_classes'] = array(
	'WordPressdotorg\Plugin_Directory\Readme\Parser' => __DIR__ . '/vendor/class-parser.php',

	'Fragen\Singleton' => __DIR__ . '/src/Singleton.php',
	'Parsedown'        => __DIR__ . '/vendor/parsedown/Parsedown.php',
	'PAnD'             => __DIR__ . '/vendor/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php',
);

// Load Autoloader.
require_once __DIR__ . '/src/Autoloader.php';
$ghu['loader'] = 'Fragen\\Autoloader';
new $ghu['loader']( $ghu['root'], $ghu['extra_classes'] );

// Instantiate class GitHub_Updater.
$ghu['instantiate'] = 'Fragen\\GitHub_Updater\\Init';
$ghu['init']        = new $ghu['instantiate'];
$ghu['init']->run();

/**
 * Initialize Persist Admin notices Dismissal.
 *
 * @link https://github.com/collizo4sky/persist-admin-notices-dismissal
 */
add_action( 'admin_init', array( 'PAnD', 'init' ) );

// TODO: move in a better place
register_activation_hook( __FILE__, array( 'Fragen\\GitHub_Updater\\Init', 'install') );
add_action('plugins_loaded', array( 'Fragen\\GitHub_Updater\\Rest_Log_Table', 'update_db_table')); // Trick to update database version of the plugin
//

/**
 * Locate Frontend Template Files
 *
 * Locate the called template.
 * Search Order:
 * 1. /wp-content/themes/theme/plugins/github-updater/templates/$template_name
 * 2. /wp-content/plugins/github-updater/src/templates/$template_name.
 *
 * @link https://jeroensormani.com/how-to-add-template-files-in-your-plugin/
 *
 * @param 	string 	$template_name			Template to load.
 * @param 	string 	$string $template_path	Path to templates.
 * @param 	string	$default_path			Default path to template files.
 * @return 	string 							Path to the template file.
 *
 */
function ghu_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	// Set variable to search in templates folder of theme.
	if ( ! $template_path ) :
		$template_path = 'plugins/github-updater/templates/';
	endif;
	// Set default plugin templates path.
	if ( ! $default_path ) :
		$default_path = plugin_dir_path( __FILE__ ) . 'src/templates/'; // Path to the template folder
	endif;
	// Search template file in theme folder.
	$template = locate_template( array( $template_path . $template_name ) );
	// Get plugins template file.
	if ( ! $template ) :
		$template = $default_path . $template_name;
	endif;
	return apply_filters( 'ghu_locate_template', $template, $template_name, $template_path, $default_path );
}
