<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */
?>

 <?php if ( ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) && is_multisite() ): ?>
	 <div class="updated">
		 <p><?php esc_html_e( 'Settings saved.', 'github-updater' ); ?></p>
	 </div>
 <?php elseif ( isset( $_GET['reset'] ) && '1' === $_GET['reset'] ): ?>
	 <div class="updated">
		 <p><?php esc_html_e( 'RESTful key reset.', 'github-updater' ); ?></p>
	 </div>
 <?php elseif ( isset( $_GET['refresh_transients'] ) && '1' === $_GET['refresh_transients'] ) : ?>
	 <div class="updated">
		 <p><?php esc_html_e( 'Cache refreshed.', 'github-updater' ); ?></p>
	 </div>
 <?php endif; ?>
