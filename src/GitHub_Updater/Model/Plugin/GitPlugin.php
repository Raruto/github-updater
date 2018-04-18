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

//use Gitonomy\Git\Repository;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class GitPlugin extends WordpressPlugin {
	private $repository;

	public function __construct( $plugin_path, $plugin_data ) {
		parent::__construct( $plugin_path, $plugin_data );
		//$this->repository = new Repository( $this->path ); // REQUIRES: Gitonomy, Symfony
		// Parse config file with sections
		$this->config = parse_ini_file( $this->path . ".git/config", true );
	}

	public function get_version() {
		$version = $this->plugin_data['Version'];
		if ( $this->has_composer() ) {
			$composer = $this->get_composer();
			if ( !empty( $composer->version ) ) {
				return $composer->version;
			}
		}
		return $version;
	}

	public function get_required_version() {
		$version = '>='.$this->plugin_data['Version'];
		if ( $this->has_composer() ) {
			$composer = $this->get_composer();
			if ( !empty( $composer->version ) ) {
				if (  is_numeric( $composer->version ) )
					return '~'.$composer->version;
				return $composer->version;
			}
		}
		return $version;
	}

	public function is_packagist() {
		return false;
	}

	public function has_vcs() {
		return true;
	}

	public function get_vcs_type() {
		return 'git';
	}

	public function get_url() {
		if ( $this->has_composer() ) {
			//wp_die( 'omg composer'.$this->get_name() );
		}
		// get the repository URL
		// $remote_url = $this->repository->run( 'config', array( '--get' => 'remote.origin.url' ) ); // REQUIRES: Gitonomy, Symfony
		$remote_url = ($this->config['remote origin']['url']);
		$remote_url = trim( $remote_url );
		return $remote_url;
	}
}
