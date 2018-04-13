<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

namespace Fragen\GitHub_Updater\Model\Plugin;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class PluginFactory
{
		protected $plugin_path;
		protected $plugin_data;

		public function __construct( $plugin_path, $plugin_data ) {
			$this->plugin_path = $plugin_path;
			$this->plugin_data = $plugin_data;
		}

		public function create( $type = "github-updater") {

		switch ($type) {
			case 'github-updater':
				$plugin = $this->create_GHUObj();
				break;
			case 'composer':
				$plugin = $this->create_ComposerObj();
				break;
			}

			return $plugin;

		}

		protected function create_GHUObj() {
			$plugin = array();

			$plugin_path = $this->plugin_path;
			$plugin_data =  $this->plugin_data;

			$slug = $plugin_path;											// eg. akismet/akismet.php
			$path = plugin_dir_path( $plugin_path );	// eg. akismet/
			$folder_name = basename($path);						// eg. akismet

			$github = false;
			$gitlab = false;
			$bitbucket = false;
			$gitea = false;
			$wp_org = false;

			// Trying to detect plugin's source

			if( !empty($plugin_data['GitHub Plugin URI']) )
				$github = true;
			if( !empty($plugin_data['GitLab Plugin URI']) )
				$gitlab = true;
			if( !empty($plugin_data['Bitbucket Plugin URI']) )
				$bitbucket = true;
			if( !empty($plugin_data['Gitea Plugin URI']) )
				$gitea = true;
			if( !empty($plugin_data['PluginURI']) ){

				// Becasue some plugins doesn't use GitHub Updater Headers
				// we need to check if it could come from a different source
				// other than wp.org

				if( strpos($plugin_data['PluginURI'], "wordpress.org") > 0 )
					$wp_org = true;
				else if( strpos($plugin_data['PluginURI'], "github.com") > 0 )
					$github = true;
				else if( strpos($plugin_data['PluginURI'], "gitlab.com") > 0 )
					$gitlab = true;
				else if( strpos($plugin_data['PluginURI'], "bitbucket.org") > 0 )
					$bitbucket = true;
				else if( strpos($plugin_data['PluginURI'], "gitea.io") > 0 )
					$gitea = true;

			}

			// Populate plugin array

			if( $wp_org ) {
				$plugin['name'] = $folder_name;
				$plugin['host'] = "wordpress";
				$plugin['slug'] = $plugin_path;
				$plugin['uri'] = $plugin_data['PluginURI'];
				$plugin['version'] = $plugin_data['Version'];
				$plugin['optional'] = !is_plugin_active( $plugin_path ); // Optional if the plugin is inactive
			}
			if( $github ) {
				$plugin['name'] = $folder_name;
				$plugin['host'] = "github";
				$plugin['slug'] = $plugin_path;
				$plugin['uri'] = !empty($plugin_data['GitHub Plugin URI']) ? $plugin_data['GitHub Plugin URI'] : $plugin_data['PluginURI'];
				$plugin['branch'] = !empty($plugin_data['GitHub Branch']) ? $plugin_data['GitHub Branch'] : "master";
				$plugin['version'] = $plugin_data['Version'];
				$plugin['token'] = !empty($plugin_data['GitHub Access Token']) ? $plugin_data['GitHub Access Token'] : null;
				$plugin['optional'] = !is_plugin_active( $plugin_path ); // Optional if the plugin is inactive
			}
			if( $gitlab ) {
				$plugin['name'] = $folder_name;
				$plugin['host'] = "gitlab";
				$plugin['slug'] = $plugin_path;
				$plugin['uri'] = !empty($plugin_data['GitLab Plugin URI']) ? $plugin_data['GitLab Plugin URI'] : $plugin_data['PluginURI'];
				$plugin['branch'] = !empty($plugin_data['GitLab Branch']) ? $plugin_data['GitLab Branch'] : "master";
				$plugin['version'] = $plugin_data['Version'];
				$plugin['token'] = !empty($plugin_data['GitLab Access Token']) ? $plugin_data['GitLab Access Token'] : null;
				$plugin['optional'] = !is_plugin_active( $plugin_path ); // Optional if the plugin is inactive
			}
			if( $bitbucket ) {
				$plugin['name'] = $folder_name;
				$plugin['host'] = "bitbucket";
				$plugin['slug'] = $plugin_path;
				$plugin['uri'] = !empty($plugin_data['Bitbucket Plugin URI']) ? $plugin_data['Bitbucket Plugin URI'] : $plugin_data['PluginURI'];
				$plugin['branch'] = !empty($plugin_data['Bitbucket Branch']) ? $plugin_data['Bitbucket Branch'] : "master";
				$plugin['version'] = $plugin_data['Version'];
				$plugin['token'] = !empty($plugin_data['Bitbucket Access Token']) ? $plugin_data['Bitbucket Access Token'] : null;
				$plugin['optional'] = !is_plugin_active( $plugin_path ); // Optional if the plugin is inactive
			}
			if( $gitea ) {
				$plugin['name'] = $folder_name;
				$plugin['host'] = "gitea";
				$plugin['slug'] = $plugin_path;
				$plugin['uri'] = !empty($plugin_data['Gitea Plugin URI']) ? $plugin_data['Gitea Plugin URI'] : $plugin_data['PluginURI'];
				$plugin['branch'] = !empty($plugin_data['Gitea Branch']) ? $plugin_data['Gitea Branch'] : "master";
				$plugin['version'] = $plugin_data['Version'];
				$plugin['token'] = !empty($plugin_data['Gitea Access Token']) ? $plugin_data['Gitea Access Token'] : null;
				$plugin['optional'] = !is_plugin_active( $plugin_path ); // Optional if the plugin is inactive
			}

			if ( $github == false && $gitlab == false && $bitbucket == false && $gitea == false & $wp_org == false ) {
				$plugin['name'] =  $folder_name;
				$plugin['host'] = "uknown";
				$plugin['slug'] = $plugin_path;
				//$plugin['uri'] = $plugin_data['PluginURI'];
				$plugin['version'] = $plugin_data['Version'];
				$plugin['optional'] = !is_plugin_active( $plugin_path ); // Optional if the plugin is inactive
			}

			return $plugin;

		}

		protected function create_ComposerObj() {
			// if ( !is_plugin_active( $plugin_path ) ) {
			// 	return;
			// }

			$plugin_path = $this->plugin_path;
			$plugin_data =  $this->plugin_data;

			$path = plugin_dir_path( $plugin_path );							// eg. akismet/akismet.php
			$fullpath = WP_CONTENT_DIR.'/plugins/'.$path;					// eg. www.example.com/wp-content/plugins/akismet/

			if ( file_exists( $fullpath.'.git/' ) ) {
				$dev_plugin = new GitPlugin( $plugin_path, $plugin_data );
			} else if ( file_exists( $fullpath.'.hg/' ) ) {
				$dev_plugin = new MercurialPlugin( $plugin_path, $plugin_data );
			} else if ( file_exists( $fullpath.'.svn/' ) ) {
				$dev_plugin = new SubversionPlugin( $plugin_path, $plugin_data );
			} else {
				$dev_plugin = new WPackagistPlugin( $plugin_path, $plugin_data );
			}

			if( isset($dev_plugin) ) {
				return $dev_plugin;
			}

		}
}
