<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

namespace Fragen\GitHub_Updater\Model\JSON;

use Fragen\GitHub_Updater\Model\Plugin\PluginInterface;
use Fragen\GitHub_Updater\Model\Plugin\PluginFactory;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class GHU Model
 *
 * Class that will handle custom json object "github-updater.json"
 * based on: https://github.com/tomjn/composerpress
 *
 * @package Fragen\GitHub_Updater
 */
class GHUModel extends JSONModel
{

	public $plugins;

	public function __construct() {
		$this->plugins = array();

		$this->initialize_json_manifest();
	}

	public function add_plugin( $plugin_path, $plugin_data ) {
		$factory = new PluginFactory( $plugin_path, $plugin_data );

		$plugin = $factory->create( "github-updater" );	// returns an Array() Object (TODO: use classes instead..)

		//populate "github-updater.json"

		$this->plugins[] = $plugin;
	}

	public function initialize_json_manifest() {

	}

	public function finalize_json_manifest() {
		$manifest = array();
		$manifest = $this->plugins;
		return $manifest;
	}

}
