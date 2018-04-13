<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

namespace Fragen\GitHub_Updater\Model;

use Fragen\GitHub_Updater\Model\Plugin\PluginInterface;
use Fragen\GitHub_Updater\Model\Plugin\PluginFactory;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class JSON Model
 *
 * Class that will handle custom json object "github-updater.json"
 * based on: https://github.com/tomjn/composerpress
 *
 * @package Fragen\GitHub_Updater
 */
class JSON_Model
{

	// GitHub Updater
	public $plugins;

	// Composer
	public $required;
	public $repos;
	public $homepage;
	public $description;
	public $version;
	public $name;
	public $extra;
	public $license;

	public function __construct() {

		// Init GitHub Updater Stuff

		$this->plugins = array();

		// Init Composer Stuff

		$this->required = array();
		$this->repos = array();
		$this->extra = array();
		$this->homepage = '';
		$this->description = '';
		$this->version = '';
		$this->name = '';
		$this->license = '';

		$this->initialize_composer_manifest();
	}

	public function fill() {
		$installed_plugins = get_plugins();
		foreach ( $installed_plugins as $plugin_path => $plugin_data ) {
			$this->add_plugin($plugin_path, $plugin_data);
		}
		//var_dump($installed_plugins);
	}

	public function add_plugin( $plugin_path, $plugin_data ) {

		// THINGS TO REMEMBER:
		// $slug = $plugin_path;																// eg. akismet/akismet.php
		// $path = plugin_dir_path( $plugin_path );							// eg. akismet/
		// $folder_name = basename($path);											// eg. akismet
		// $fullpath = WP_CONTENT_DIR.'/plugins/'.$path;				// eg. www.example.com/wp-content/plugins/akismet/
		// $filepath = WP_CONTENT_DIR.'/plugins/'.$plugin_path;	// eg. www.example.com/wp-content/plugins/akismet/akismet.php

		$factory = new PluginFactory( $plugin_path, $plugin_data );

		$plugin = $factory->create( "github-updater" );	// returns an Array() Object (TODO: use classes instead..)
		$this->add_ghu_plugin( $plugin );								//populate "github-updater.json"

		$plugin = $factory->create( "composer" );				// returns a PluginInterface Instance
		$this->add_composer_plugin( $plugin );					//populate "composer.json"

	}

	public function add_ghu_plugin( $plugin = array() ) {
		$this->plugins[] = $plugin;
	}

	public function add_composer_plugin( PluginInterface $plugin ) {
		$remote_url = $plugin->get_url();
		$reference = $plugin->get_reference();
		$reponame = $plugin->get_name();
		$version = $plugin->get_version();
		$required_version = $plugin->get_required_version();
		if ( empty( $version ) ) {
			$required_version = 'dev-master';
		}
		$vcstype = $plugin->get_vcs_type();

		if ( !$plugin->is_packagist() ) {
			if ( $plugin->has_composer() ) {
				$this->add_repository( $vcstype, $remote_url, $reference );
				if ( !empty( $version ) ) {
					$version = $required_version;
				}
				$this->required( $reponame, $version );
			} else {
				$source = array(
					'url' => $remote_url,
					'type' => $vcstype
				);
				$source['reference'] = $reference;
				$package = array(
					'name' => $reponame,
					'version' => 'dev-master',
					'type' => 'wordpress-plugin',
					'source' => $source
				);
				$this->add_package_repository( $package );
				$this->required( $reponame, 'dev-master' );
			}
		} else {
			$this->required( $reponame, $version );
		}
	}

	public function set_homepage( $homepage ) {
		$this->homepage = $homepage;
	}

	public function set_name( $name ) {
		$this->name = $name;
	}

	public function set_version( $version ) {
		$this->version = $version;
	}

	public function set_description( $description ) {
		$this->description = $description;
	}

	public function set_license( $license ) {
		$this->license = $license;
	}

	public function add_repository( $type, $url, $reference = '' ) {
		$source = array(
			'type' => $type,
			'url' => $url.$reference
		);

		/*if ( !empty( $reference ) ) {
			$source['reference'] = trailingslashit( $reference );
		}*/
		$this->repos[] = $source;
	}

	public function add_package_repository( $package ) {
		$this->repos[] = array(
			'type' => 'package',
			'package' => $package
		);
	}

	public function add_extra( $name, $data ) {
		$this->extra[$name] = $data;
	}

	public function required( $package, $version ) {
		$this->required[ $package ] = $version;
	}

	public function initialize_composer_manifest() {
		$this->required( 'johnpbloch/wordpress', '*@stable' );
		$this->required( 'php', '>=5.3.2' );

		$this->set_name( 'wpsite/'.sanitize_title( get_bloginfo( 'name' ) ) );
		$this->set_homepage( home_url() );
		$description = get_bloginfo( 'description' );
		$this->set_description( $description );
		$this->set_license( 'GPL-2.0+' );
		$this->set_version( get_bloginfo( 'version' ) );

		$this->add_repository( 'composer', 'http://wpackagist.org' );
	}

	public function finalize_composer_manifest() {
		$manifest = array();
		$manifest['name'] = $this->name;
		if ( !empty( $this->description ) ) {
			$manifest['description'] = $this->description;
		}
		if ( !empty( $this->license ) ) {
			$manifest['license'] = $this->license;
		}
		if ( !empty( $this->homepage ) ) {
			$manifest['homepage'] = $this->homepage;
		}
		$manifest['version'] = $this->version;
		if ( !empty( $this->repos ) ) {
			$manifest['repositories'] = $this->repos;
		}
		if ( !empty( $this->extra ) ) {
			$manifest['extra'] = $this->extra;
		}
		if ( !empty( $this->required ) ) {
			$manifest['require'] = $this->required;
		}
		$manifest['minimum-stability'] = 'dev';

		return $manifest;
	}

	public function to_json( $json_type = "github-updater" ) {

		switch ($json_type) {
			case 'github-updater':
				$json_object = $this->plugins;
				break;
			case 'composer':
				$json_object = $this->finalize_composer_manifest();
				break;
		}

		return json_encode( $json_object, ( JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );;
	}
}
