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

class GiteaPlugin extends GitPlugin {

	public function __construct( $plugin_path, $plugin_data ) {
		parent::__construct( $plugin_path, $plugin_data );

		if(!empty($plugin_data['Gitea Plugin URI'])) {
			$this->set_plugin_uri($plugin_data['Gitea Plugin URI']);
		}
		if(!empty($plugin_data['Gitea Branch'])) {
			$this->set_branch($plugin_data['Gitea Branch']);
		}
		if(!empty($plugin_data['Gitea Access Token'])) {
			$this->set_token($plugin_data['Gitea Access Token']);
		}
		$this->set_hostname("gitea");

	}

}
