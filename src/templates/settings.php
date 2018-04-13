<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

 $this->options_sub_tabs();
 ?>
 <form class="settings" method="post" action="<?php esc_attr_e( $action ); ?>">
	 <?php
	 settings_fields( 'github_updater' );
	 switch ( $subtab ) {
		 case 'github':
		 case 'bitbucket':
		 case 'bbserver':
		 case 'gitlab':
		 case 'gitea':
			 do_settings_sections( 'github_updater_' . $subtab . '_install_settings' );
			 $this->display_ghu_repos( $subtab );
			 $this->add_hidden_settings_sections( $subtab );
			 break;
		 default:
			 do_settings_sections( 'github_updater_install_settings' );
			 $this->add_hidden_settings_sections();
			 break;
	 }
	 submit_button();
	 ?>
 </form>
 <?php $refresh_transients = add_query_arg( array( 'github_updater_refresh_transients' => true ), $action ); ?>
 <form class="settings" method="post" action="<?php esc_attr_e( $refresh_transients ); ?>">
	 <?php submit_button( esc_html__( 'Refresh Cache', 'github-updater' ), 'primary', 'ghu_refresh_cache' ); ?>
 </form>
