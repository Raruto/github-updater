<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

use Fragen\GitHub_Updater\Rest_Log_table;

$action = add_query_arg( 'tab', $tab, $action );
$table = new Rest_Log_Table();

?>

<form class="settings" method="post" action="<?php esc_attr_e( $action ); ?>">
	<?php
	settings_fields( 'github_updater_remote_management' );
	do_settings_sections( 'github_updater_remote_settings' );
	submit_button();
	?>
</form>
<?php $reset_api_action = add_query_arg( array( 'github_updater_reset_api_key' => true ), $action ); ?>
<form class="settings no-sub-tabs" method="post" action="<?php esc_attr_e( $reset_api_action ); ?>">
	<?php submit_button( esc_html__( 'Reset RESTful key', 'github-updater' ) ); ?>
</form>
<?php $table->output(); ?>
