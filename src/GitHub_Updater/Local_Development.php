<?php
/**
 * GitHub Updater
 *
 * @package GitHub_Updater
 * @author  Andy Fragen
 * @author  Matt Gibbs
 * @license GPL-2.0+
 * @link    https://github.com/afragen/github-updater
 */

namespace Fragen\GitHub_Updater;

use Fragen\GitHub_Updater\Model\Plugin\PluginInterface;
use Fragen\GitHub_Updater\Model\Plugin\PluginFactory;

/**
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Local_Development
 *
 * Places development notice for plugins or themes that are in local development.
 * Notices are placed on the plugins page and the themes page.
 * Prevents updating of selected plugins and themes.
 *
 * based on: https://github.com/afragen/local-development
 */
class Local_Development {

	/**
	 * Holds the singleton instance.
	 *
	 * @var \Local_Development
	 */
	private static $instance;

	/**
	 * Static to hold slugs of plugins under development.
	 *
	 * @var
	 */
	protected static $plugins_slugs;

	/**
	 * Static to hold slugs themes under development.
	 *
	 * @var
	 */
	protected static $themes_slugs;

	/**
	 * Static to hold message.
	 *
	 * @var
	 */
	protected static $message;

	/**
	 * Holds plugin data.
	 *
	 * @var
	 */
	protected $plugins;

	/**
	 * Holds theme data.
	 *
	 * @var
	 */
	protected $themes;

	/**
	 * Holds plugin settings.
	 *
	 * @var mixed|void
	 */
	protected static $config;

	/**
	 * Holds the plugin basename.
	 *
	 * @var string
	 */
	private $plugin_slug = 'github-updater/github-updater.php';

	/**
	 * Local_Development constructor.
	 */
	public function __construct() {

		// $this->settings_constructor();
		$config = get_site_option( 'local_development' );

		/*
		 * Skip on heartbeat or if no saved settings.
		 */
		if ( ( isset( $_POST['action'] ) && 'heartbeat' === $_POST['action'] ) || ! $config ) {
			return false;
		}

		self::$config = $config;

		self::$plugins_slugs = isset( $config['plugins'] ) ? $config['plugins'] : null;
		self::$themes_slugs  = isset( $config['themes'] ) ? $config['themes'] : null;
		self::$message       = esc_html__( 'In Local Development', 'github-updater' );

		add_filter( 'plugin_row_meta', array( &$this, 'row_meta' ), 15, 2 );
		add_filter( 'site_transient_update_plugins', array( &$this, 'hide_update_nag' ), 15, 1 );

		add_filter( 'theme_row_meta', array( &$this, 'row_meta' ), 15, 2 );
		add_filter( 'site_transient_update_themes', array( &$this, 'hide_update_nag' ), 15, 1 );

		if ( ! is_multisite() ) {
			add_filter( 'wp_prepare_themes_for_js', array( &$this, 'set_theme_description' ), 15, 1 );
		}

		// GitHub Link Stuff
		add_filter( 'extra_plugin_headers', array( &$this, 'GHL_extra_headers' ) );
		/*
		 * Ensure get_plugins() function is available.
		 */
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
		$plugins = get_plugins();
		foreach ( array_keys( $plugins ) as $plugin_basename ) {
			add_filter( "plugin_action_links_{$plugin_basename}", array( &$this, 'GHL_plugin_link' ), 1000, 4 );
			add_filter( "network_admin_plugin_action_links_{$plugin_basename}", array( &$this, 'GHL_plugin_link' ), 1000, 4 );
		}

	}

	/**
	 * Singleton.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run.
	 */
	public function run() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'page_init' ) );
		add_action( 'admin_head-themes.php', array( &$this, 'hide_update_message' ) );
		add_action( 'admin_head-plugins.php', array( &$this, 'hide_update_message' ) );

		add_filter(
			is_multisite() ? 'network_admin_plugin_action_links_' . $this->plugin_slug : 'plugin_action_links_' . $this->plugin_slug, array(
				&$this,
				'plugin_action_links',
			)
		);

	}

	/**
	 * Add an additional element to the row meta links.
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function row_meta( $links, $file ) {
		if ( ( ! empty( self::$plugins_slugs ) && array_key_exists( $file, self::$plugins_slugs ) ) ||
				 ( ! empty( self::$themes_slugs ) && array_key_exists( $file, self::$themes_slugs ) )
		) {
			$links[] = '<strong>' . self::$message . '</strong>';
		}

		return $links;
	}

	/**
	 * Sets the description for the single install theme action.
	 *
	 * @param $prepared_themes
	 *
	 * @return array
	 */
	public function set_theme_description( $prepared_themes ) {
		foreach ( $prepared_themes as $theme ) {
			if ( array_key_exists( $theme['id'], (array) self::$themes_slugs ) ) {
				$message                                        = wp_get_theme( $theme['id'] )->get( 'Description' );
				$message                                       .= '<p><strong>' . self::$message . '</strong></p>';
				$prepared_themes[ $theme['id'] ]['description'] = $message;
			}
		}

		return $prepared_themes;
	}

	/**
	 * Hide the update nag.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function hide_update_nag( $value ) {
		switch ( current_filter() ) {
			case 'site_transient_update_plugins':
				$repos = self::$plugins_slugs;
				break;
			case 'site_transient_update_themes':
				$repos = self::$themes_slugs;
				break;
			default:
				return $value;
		}

		if ( ! empty( $repos ) ) {
			foreach ( array_keys( $repos ) as $repo ) {
				if ( 'update_nag' === $repo ) {
					continue;
				}
				if ( isset( $value->response[ $repo ] ) ) {
					unset( $value->response[ $repo ] );
				}
			}
		}

		return $value;
	}

	/**
	 * Settings constructor.
	 */
	public function settings_constructor() {
		// add_action( 'init', array( &$this, 'init' ) );
		// self::$config = get_site_option( 'local_development' );
		// add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', array( &$this, 'add_plugin_page' ) );
		add_action( 'network_admin_edit_local-development', array( &$this, 'update_network_settings' ) );
		// add_action( 'admin_init', array( &$this, 'page_init' ) );
		add_action( 'admin_head-settings_page_local-development', array( &$this, 'style_settings' ) );
		// add_action( 'admin_head-themes.php', array( &$this, 'hide_update_message' ) );
		// add_action( 'admin_head-plugins.php', array( &$this, 'hide_update_message' ) );
		//
		// add_filter( is_multisite() ? 'network_admin_plugin_action_links_' . $this->plugin_slug : 'plugin_action_links_' . $this->plugin_slug, array(
		// &$this,
		// 'plugin_action_links',
		// ) );
	}

	/**
	 * Initialize plugin/theme data. Needs to be called in the 'init' hook.
	 */
	public function init() {

		self::$config = get_site_option( 'local_development' );

		$plugins = array();
		$themes  = array();

		/*
		 * Ensure get_plugins() function is available.
		 */
		include_once ABSPATH . '/wp-admin/includes/plugin.php';

		$this->plugins = get_plugins();
		$this->themes  = wp_get_themes( array( 'errors' => null ) );

		foreach ( array_keys( $this->plugins ) as $slug ) {
			$plugins[ $slug ] = $this->plugins[ $slug ]['Name'];
		}
		$this->plugins = $plugins;

		foreach ( array_keys( $this->themes ) as $slug ) {
			$themes[ $slug ] = $this->themes[ $slug ]->get( 'Name' );
		}
		$this->themes = $themes;

		// Automatically detect local developed plugins
		$installed_plugins = get_plugins();
		foreach ( $installed_plugins as $plugin_path => $plugin_data ) {
			$factory = new PluginFactory( $plugin_path, $plugin_data );
			$plugin  = $factory->create();
			if ( isset( $plugin ) && $plugin instanceof PluginInterface && $plugin->is_in_development() ) {
				self::$config['plugins'][ $plugin_path ] = '1';
			}
		}
		update_site_option( 'local_development', self::$config );
	}

	/**
	 * Define tabs for Settings page.
	 * By defining in a method, strings can be translated.
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _settings_tabs() {
		return array(
			'local_dev_settings_plugins' => esc_html__( 'Plugins', 'github-updater' ),
			'local_dev_settings_themes'  => esc_html__( 'Themes', 'github-updater' ),
		);
	}

	/**
	 * Add options page.
	 */
	public function add_plugin_page() {
		if ( is_multisite() ) {
			add_submenu_page(
				'settings.php',
				esc_html__( 'Local Development Settings', 'github-updater' ),
				esc_html__( 'Local Development', 'github-updater' ),
				'manage_network',
				'github-updater',
				array( &$this, 'create_admin_page' )
			);
		} else {
			add_options_page(
				esc_html__( 'Local Development Settings', 'github-updater' ),
				esc_html__( 'Local Development', 'github-updater' ),
				'manage_options',
				'github-updater',
				array( &$this, 'create_admin_page' )
			);
		}
	}

	/**
	 * Renders setting tabs.
	 *
	 * Walks through the object's tabs array and prints them one by one.
	 * Provides the heading for the settings page.
	 *
	 * @access private
	 */
	private function _options_tabs() {
		$current_tab = isset( $_GET['sub'] ) ? $_GET['sub'] : 'local_dev_settings_plugins';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->_settings_tabs() as $key => $name ) {
			$active = ( $current_tab == $key ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=github-updater&tab=github_updater_local_development&sub=' . $key . '">' . $name . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * Options page callback.
	 */
	public function create_admin_page() {

		$this->settings_constructor(); // TODO: check if really need this

		$action = is_multisite() ? 'edit.php?action=github-updater' : 'options.php';
		$tab    = isset( $_GET['sub'] ) ? $_GET['sub'] : 'local_dev_settings_plugins';
		?>
		<div class="wrap">
			<h2>
				<?php esc_html_e( 'Local Development Settings', 'github-updater' ); ?>
			</h2>
			<p>Selected repositories will not display an update notice.</p>
			<?php $this->_options_tabs(); ?>
			<?php if ( isset( $_GET['updated'] ) && true == $_GET['updated'] ) : ?>
					<div class="updated"><p><strong><?php esc_html_e( 'Saved.', 'github-updater' ); ?></strong></p></div>
				<?php endif; ?>
			<?php if ( 'local_dev_settings_plugins' === $tab ) : ?>
					<form method="post" action="<?php esc_attr_e( $action ); ?>">
						<?php
						settings_fields( 'local_development_settings' );
						do_settings_sections( 'local_dev_plugins' );
						submit_button();
						?>
					</form>
				<?php endif; ?>

				<?php if ( 'local_dev_settings_themes' === $tab ) : ?>
					<?php $action = add_query_arg( 'sub', $tab, $action ); ?>
					<form method="post" action="<?php esc_attr_e( $action ); ?>">
						<?php
						settings_fields( 'local_development_settings' );
						do_settings_sections( 'local_dev_themes' );
						submit_button();
						?>
					</form>
				<?php endif; ?>
				<p><strong>NB.</strong> plugins that contain a dev-folder (eg <strong>.git/</strong>,<strong>.svn/</strong>, <strong>.hg/</strong>) are automatically included in this list.</p>
			</div>
			<?php
	}

	/**
	 * Register and add settings.
	 */
	public function page_init() {

		/*
		 * Plugin settings.
		 */
		register_setting(
			'local_development_settings',
			'local_dev_plugins',
			array( &$this, 'sanitize' )
		);

		add_settings_section(
			'local_dev_plugins',
			esc_html__( 'Plugins', 'github-updater' ),
			array( &$this, 'print_section_plugins' ),
			'local_dev_plugins'
		);

		foreach ( $this->plugins as $id => $name ) {
			add_settings_field(
				$id,
				null,
				array( &$this, 'token_callback_checkbox' ),
				'local_dev_plugins',
				'local_dev_plugins',
				array(
					'id'   => $id,
					'type' => 'plugins',
					'name' => $name,
				)
			);
		}

		/*
		 * Theme settings.
		 */
		register_setting(
			'local_development_settings',
			'local_dev_themes',
			array( &$this, 'sanitize' )
		);

		add_settings_section(
			'local_dev_themes',
			esc_html__( 'Themes', 'github-updater' ),
			array( &$this, 'print_section_themes' ),
			'local_dev_themes'
		);

		foreach ( $this->themes as $id => $name ) {
			add_settings_field(
				$id,
				null,
				array( &$this, 'token_callback_checkbox' ),
				'local_dev_themes',
				'local_dev_themes',
				array(
					'id'   => $id,
					'type' => 'themes',
					'name' => $name,
				)
			);
		}

		$this->update_settings();
	}

	/**
	 * Print the plugin text.
	 */
	public function print_section_plugins() {
		esc_html_e( 'Select the locally developed plugins.', 'github-updater' );
	}

	/**
	 * Print the theme text.
	 */
	public function print_section_themes() {
		esc_html_e( 'Select the locally developed themes.', 'github-updater' );
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public static function sanitize( $input ) {
		$new_input = array();
		foreach ( array_keys( (array) $input ) as $id ) {
			$new_input[ sanitize_text_field( $id ) ] = sanitize_text_field( $input[ $id ] );
		}

		return $new_input;
	}

	/**
	 * Get the settings option array and print one of its values.
	 *
	 * @param $args
	 */
	public function token_callback_checkbox( $args ) {
		$checked = isset( self::$config[ $args['type'] ][ $args['id'] ] ) ? esc_attr( self::$config[ $args['type'] ][ $args['id'] ] ) : null;
		?>
		<label for="<?php esc_attr_e( $args['id'] ); ?>">
			<input type="checkbox" name="local_dev[<?php esc_attr_e( $args['id'] ); ?>]" value="1" <?php checked( '1', $checked, true ); ?> >
			<?php esc_html_e( $args['name'] ); ?>
			</label>
			<?php
	}

	/**
	 * Update settings for single install.
	 */
	public function update_settings() {

		if ( ! isset( $_POST['_wp_http_referer'] ) || is_multisite() ) {
			return false;
		}
		$query = parse_url( $_POST['_wp_http_referer'], PHP_URL_QUERY );
		parse_str( $query, $arr );
		if ( empty( $arr['sub'] ) ) {
			$arr['sub'] = 'local_dev_settings_plugins';
		}

		if ( isset( $_POST['option_page'] ) &&
				 'local_development_settings' === $_POST['option_page']
		) {
			if ( 'local_dev_settings_plugins' === $arr['sub'] ) {
				self::$config['plugins'] = self::sanitize( $_POST['local_dev'] );
			}
			if ( 'local_dev_settings_themes' === $arr['sub'] ) {
				self::$config['themes'] = self::sanitize( $_POST['local_dev'] );
			}
			update_site_option( 'local_development', self::$config );
		}
	}

	/**
	 * Update network settings.
	 * Used when plugin is network activated to save settings.
	 *
	 * @link http://wordpress.stackexchange.com/questions/64968/settings-api-in-multisite-missing-update-message
	 * @link http://benohead.com/wordpress-network-wide-plugin-settings/
	 */
	public function update_network_settings() {

		$query = parse_url( $_POST['_wp_http_referer'], PHP_URL_QUERY );
		parse_str( $query, $arr );
		if ( empty( $arr['sub'] ) ) {
			$arr['sub'] = 'local_dev_settings_plugins';
		}

		if ( 'local_development_settings' === $_POST['option_page'] ) {
			if ( 'local_dev_settings_plugins' === $arr['sub'] ) {
				self::$config['plugins'] = self::sanitize( $_POST['local_dev'] );
			}
			if ( 'local_dev_settings_themes' === $arr['sub'] ) {
				self::$config['themes'] = self::sanitize( $_POST['local_dev'] );
			}
			update_site_option( 'local_development', self::$config );
		}

		$location = add_query_arg(
			array(
				'page'    => 'github-updater',
				'updated' => 'true',
				'tab'     => 'github_updater_local_development',
				'sub'     => $arr['sub'],
			),
			network_admin_url( 'settings.php' )
		);
		wp_redirect( $location );
		exit;
	}

	/**
	 * Add setting link to plugin page.
	 * Applied to the list of links to display on the plugins page (beside the activate/deactivate links).
	 *
	 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$settings_page = is_multisite() ? 'settings.php' : 'options-general.php';
		$link          = array( '<a href="' . esc_url( network_admin_url( $settings_page ) ) . '?page=github-updater">' . esc_html__( 'Settings', 'github-updater' ) . '</a>' );

		return array_merge( $links, $link );
	}

	/**
	 * Style settings.
	 */
	public function style_settings() {
		?>
		<!-- Local Development -->
		<style>
			.form-table th[scope='row']:empty {
				display: none;
			}
		</style>
		<?php
	}

	/**
	 * Hide update messages for GitHub Updater.
	 */
	public function hide_update_message() {
		global $pagenow;
		if ( ! class_exists( 'Fragen\\GitHub_Updater\\Base' ) ) {
			return;
		}

		if ( 'plugins.php' === $pagenow && ! empty( self::$config['plugins'] ) ) {
			foreach ( array_keys( self::$config['plugins'] ) as $plugin ) {
				$css[] = '[data-slug="' . dirname( $plugin ) . '"] div.update-message';
			}
		}
		if ( 'themes.php' === $pagenow && ! empty( self::$config['themes'] ) ) {
			foreach ( array_keys( self::$config['themes'] ) as $theme ) {
				$css[] = '[data-slug="' . $theme . '"] div.update-message';
				$css[] = '#' . $theme . ' div.update-message';
			}
		}

		if ( empty( $css ) ) {
			return;
		}

		$css = implode( ', ', $css );
		?>
		<!-- Local Development -->
		<style>
			<?php echo $css; ?> {
				display: none;
			}
			</style>
			<?php
	}

	function GHL_extra_headers( $extra_headers ) {
		// Keys will get lost.
		return array_merge(
			$extra_headers, array(
				//'GitHubURI'       => 'GitHub Plugin URI',
				'GitHubBranch'    => 'GitHub Branch',
				'GitHubToken'     => 'GitHub Access Token',
				//'GitLabURI'       => 'GitLab Plugin URI',
				'GitLabBranch'    => 'GitLab Branch',
				//'BitbucketURI'    => 'Bitbucket Plugin URI',
				'BitbucketBranch' => 'Bitbucket Branch',
			)
		);
	}

	function GHL_plugin_link( $actions, $plugin_file, $plugin_data, $context ) {

		// No GitHub data during search installed plugins.
		if ( 'search' === $context ) {
			return $actions;
		}

			var_dump( $plugin_data );

		$link_template = '<a href="%s" title="%s" target="_blank"><img src="%s" style="width: 16px; height: 16px; margin-top: 4px; padding-right: 4px; float: none;" height="16" width="16" alt="%s" />%s</a>';

		$on_wporg = false;
		_maybe_update_plugins();
		$plugin_state = get_site_transient( 'update_plugins' );
		if ( isset( $plugin_state->response[ $plugin_file ] )
			|| isset( $plugin_state->no_update[ $plugin_file ] )
		) {
			$on_wporg = true;
		}

		if ( ! empty( $plugin_data['GitHub Plugin URI'] ) ) {
			$icon   = 'icon/GitHub-Mark-32px.png';
			$branch = '';

			if ( ! empty( $plugin_data['GitHub Access Token'] ) ) {
				$icon = 'icon/GitHub-Mark-Private-32px.png"';
			}
			if ( ! empty( $plugin_data['GitHub Branch'] ) ) {
				$branch = '/' . $plugin_data['GitHub Branch'];
			}

			$new_action = array(
				'github' => sprintf(
					$link_template,
					$plugin_data['GitHub Plugin URI'],
					__( 'Visit GitHub repository', 'github-updater' ),
					plugins_url( $icon, GHU_PLUGIN_NAME ),
					'GitHub',
					$branch
				),
			);
			$actions    = $new_action + $actions;
		}

		if ( ! empty( $plugin_data['GitLab Plugin URI'] ) ) {
			$icon   = 'icon/GitLab-Mark-32px.png';
			$branch = '';

			if ( ! empty( $plugin_data['GitLab Branch'] ) ) {
				$branch = '/' . $plugin_data['GitLab Branch'];
			}

			$new_action = array(
				'gitlab' => sprintf(
					$link_template,
					$plugin_data['GitLab Plugin URI'],
					__( 'Visit GitLab repository', 'github-updater' ),
					plugins_url( $icon, GHU_PLUGIN_NAME ),
					'GitLab',
					$branch
				),
			);
			$actions    = $new_action + $actions;
		}

		if ( ! empty( $plugin_data['Bitbucket Plugin URI'] ) ) {
			$icon   = 'icon/bitbucket_32_darkblue_atlassian.png';
			$branch = '';

			if ( ! empty( $plugin_data['Bitbucket Branch'] ) ) {
				$branch = '/' . $plugin_data['Bitbucket Branch'];
			}

			$new_action = array(
				'bitbucket' => sprintf(
					$link_template,
					$plugin_data['Bitbucket Plugin URI'],
					__( 'Visit Bitbucket repository', 'github-updater' ),
					plugins_url( $icon, GHU_PLUGIN_NAME ),
					'Bitbucket',
					$branch
				),
			);
			$actions    = $new_action + $actions;
		}

		if ( $on_wporg ) {
			$plugin_page = '';
			if ( isset( $plugin_state->response[ $plugin_file ] ) ) {
				if ( property_exists( $plugin_state->response[ $plugin_file ], 'url' ) ) {
					$plugin_page = $plugin_state->response[ $plugin_file ]->url;
				}
			} elseif ( isset( $plugin_state->no_update[ $plugin_file ] ) ) {
				if ( property_exists( $plugin_state->no_update[ $plugin_file ], 'url' ) ) {
					$plugin_page = $plugin_state->no_update[ $plugin_file ]->url;
				}
			}

			// GHU also sets plugin->url.
			if ( false !== strstr( $plugin_page, '//wordpress.org/plugins/' ) ) {
							$icon    = 'icon/wordpress-logo-32.png';
							$branch  = '';
				$new_action          = array(
					'wordpress_org' => sprintf(
						// $wp_link_template,
											$link_template,
						$plugin_page,
						__( 'Visit WordPress.org Plugin Page', 'github-updater' ),
						plugins_url( $icon, GHU_PLUGIN_NAME ),
						'wp_org',
						$branch
					),
				);
							$actions = $new_action + $actions;
			}
		}

		return $actions;
	}

}
