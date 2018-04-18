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

class BitbucketPlugin extends GitPlugin {

	public function __construct( $plugin_path, $plugin_data ) {
		parent::__construct( $plugin_path, $plugin_data );

		if(!empty($plugin_data['Bitbucket Plugin URI'])) {
			$this->set_plugin_uri($plugin_data['Bitbucket Plugin URI']);
		}
		if(!empty($plugin_data['Bitbucket Branch'])) {
			$this->set_branch($plugin_data['Bitbucket Branch']);
		}
		if(!empty($plugin_data['Bitbucket Access Token'])) {
			$this->set_token($plugin_data['Bitbucket Access Token']);
		}
		$this->set_hostname("bitbucket");

	}

}
