<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

 $action = add_query_arg( 'tab', $tab, $action );

?>

 <p>
	 <?php
		 printf(esc_html__('FAQs are actively under construction, for any other doubts refer to: %s, %s, %s', 'github-updater'),
			 '<a href="https://github.com/afragen/github-updater/wiki" target="_blank">wiki</a>',
			 '<a href="https://github.com/afragen/github-updater/issues" target="_blank">github</a>',
			 '<a href="https://github-updater.herokuapp.com/" target="_blank">slack</a>'
		 );
	 ?>
 </p>
 <hr>
 <h3><?php print(esc_html__('Wiki Pages', 'github-updater')); ?></h3>
 <ol>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Home">Home</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/General-Usage">General Usage</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Installation">Installation</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Settings">Settings</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Usage">Usage</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Background-Processing">Background Processing</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Self-Hosted-or-Enterprise-Installations">Self-Hosted or Enterprise Installations</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Versions-and-Branches">Versions and Branches</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Language-Packs">Language Packs</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Remote-Installation">Remote Installation</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Remote-Management---RESTful-Endpoints">Remote Management / RESTful Endpoints</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/WP-CLI">WP-CLI Support</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Messages">Messages</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/WordPress.org-Directory">WordPress.org Directory</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Developer-Hooks">Developer Hooks</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Translations">Translations</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/wiki/Extras-and-Credits">Extras and Credits</a></li>
 </ol>
 <hr>
 <h3><?php print(esc_html__('Known issues', 'github-updater')); ?></h3>
 <ul style="list-style:initial; margin-left:2%;">
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/663">GitLab Webhook Timeout Issues</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/662">Bitbucket credentials not persisting</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/637">GitHubupdater keeps overwriting his own settings</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/540">Beanstalk?</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/540">Feature: Build installable zip on release</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/292">Does github-updater work with Gogs?</a></li>
 </ul>
 <hr>
 <h3><?php print(esc_html__('Calls to action', 'github-updater')); ?></h3>
 <ul style="list-style:initial; margin-left:2%;">
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/470">Translations now using Language Pack updates</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/339">Travis-CI and Unit Tests</a></li>
 </ul>
 <hr>
 <sub style="float:right; text-align:center;">
 <?php
	 printf(
		 esc_html__('GHU is a free software release under the %s', 'github-updater'),
		 '<a href="https://github.com/afragen/github-updater/blob/master/LICENSE" target="_blank">"GNU General Public License v2.0"</a>'
	 );
 ?>
 <br>
 <?php
	 printf(
		 esc_html__('feel free to %s or %s to the plugin\'s author', 'github-updater'),
		 '<a href="https://github.com/afragen/github-updater" target="_blank">contribute</a>',
		 '<a href="http://thefragens.com/github-updater-donate" target="_blank">donate</a>'
	 );
 ?>
</sub>
