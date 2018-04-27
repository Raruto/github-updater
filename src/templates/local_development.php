<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

use Fragen\GitHub_Updater\Local_Development;

$action = add_query_arg( 'tab', $tab, $action );

Local_Development::instance()->create_admin_page();
