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

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Interface JSON Model
 *
 * @package Fragen\GitHub_Updater
 */
interface JSONModelInterface
{
	public function fill();

	public function add_plugin( $plugin_path, $plugin_data );

	public function initialize_json_manifest();

	public function finalize_json_manifest();

	public function to_json();
}
