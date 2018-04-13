<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

$message = '';
if ('delete' === $table->current_action()) {
	$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'github-updater'), count($_REQUEST['id'])) . '</p></div>';
}
?>
<hr style="clear: both;">
<div class="wrap">
	<h3><?php _e('Recent Requests', 'github-updater')?></h3>

	<?php echo $message; ?>

	<p>
		<?php
				esc_html_e('Everytime someone made a call to the Rest API Endpoint we stored some basic info about the request, you can use these details to easily figure out if something went wrong (or detect an excessive use of the api).', 'github-updater');
		?>
	</p>

	<form id="persons-table" method="GET">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $table->display() ?>
	</form>

	<hr>

	<p>More info about: <a href="https://github.com/afragen/github-updater/wiki/Messages" target="_blank">Response Codes</a>, <a href="https://github.com/afragen/github-updater/wiki/Remote-Management---RESTful-Endpoints#restful-endpoints-for-remote-management" target="_blank">Rest API</a></p>

</div>
