<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

//TODO: manage better..
$settings = $this;

$logo = $settings->get_logo_url();
?>

<div class="wrap github-updater-settings">
 <h1>
	 <a href="https://github.com/afragen/github-updater" target="_blank"><img src="<?php esc_attr_e( $logo ); ?>" alt="GitHub Updater logo" /></a><br>
	 <?php esc_html_e( 'GitHub Updater', 'github-updater' ); ?>
 </h1>

<?php $settings->options_tabs(); ?>
