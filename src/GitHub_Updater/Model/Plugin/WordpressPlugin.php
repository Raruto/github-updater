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

abstract class WordpressPlugin implements PluginInterface {
	protected $path;				// eg. www.example.com/wp-content/plugins/akismet/
	protected $filepath;		// eg. www.example.com/wp-content/plugins/akismet/akismet.php
	protected $plugin_data;

	//TODO: Not all plugins are on WPackagist,
	// we should look at RepositoryInterface::findPackage
	// in the Composer APIs to ascertain if a package
	// is present or not (see also: https://github.com/Raruto/composerpress)
	const FALLBACK_VENDOR = "wpackagist-plugin";

	public function __construct( $plugin_path, $plugin_data ){

		$path = plugin_dir_path( $plugin_path );							// eg. akismet/akismet.php
		$fullpath = WP_CONTENT_DIR.'/plugins/'.$path;					// eg. www.example.com/wp-content/plugins/akismet/
		$filepath = WP_CONTENT_DIR.'/plugins/'.$plugin_path;	// eg. www.example.com/wp-content/plugins/akismet/akismet.php

		$this->path = $fullpath;
		$this->filepath = $filepath;
		$this->plugin_data = $plugin_data;
	}

	public function get_name() {

		$namespace = self::FALLBACK_VENDOR;
		$package = basename($this->path);

		$reponame = sanitize_title($namespace).'/'.sanitize_title($package);
		if ( $this->has_composer() ) {
			$composer = $this->get_composer();
			if ( !empty( $composer->name ) ) {
				return $composer->name;
			}
		}
		return $reponame;
	}

	abstract public function get_version();
	abstract public function get_required_version();

	abstract public function is_packagist();

	public function has_composer() {
		$path = trailingslashit( $this->path ).'composer.json';
		return file_exists( $path );
	}

	public function get_composer() {
		$path = trailingslashit( $this->path ).'composer.json';
		$content = file_get_contents( $path );
		$json = json_decode( $content );
		//wp_die( print_r( $json, true ) );
		return $json;
	}

	abstract public function get_vcs_type();
	abstract public function get_url();
	public function get_reference(){
		return '';
	}
}
