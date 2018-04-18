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

class MercurialPlugin extends WordpressPlugin {
	private $repository;

	public function __construct( $plugin_path, $plugin_data ) {
		parent::__construct( $plugin_path, $plugin_data );
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
				return $composer->version;
			}
		}
		return $version;
	}

	public function is_packagist() {
		return false;
	}

	public function is_in_development() {
		return file_exists( $this->path .'.hg/' );
	}

	public function has_vcs() {
		return true;
	}

	public function get_vcs_type() {
		return 'hg';
	}

	public function get_url() {
		$hgrcpath = trailingslashit( $this->path ).'.hg/hgrc';
		$hgrc = parse_ini_file( $hgrcpath );
		$remote_url = $hgrc['default'];

		$remote_url = trim( $remote_url );
		return $remote_url;
	}
}
