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

 $plugin_data = get_file_data(GHU_PLUGIN_FILE, array('Version' => 'Version'), false);
 $plugin_version = $plugin_data['Version'];

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
 <ul id="github-issues" style="list-style:initial; margin-left:2%;">
 </ul>
 <hr>
 <h3><?php print(esc_html__('Calls to action', 'github-updater')); ?></h3>
 <ul style="list-style:initial; margin-left:2%;">
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/470">Translations now using Language Pack updates</a></li>
	 <li><a target="_blank"  href="https://github.com/afragen/github-updater/issues/339">Travis-CI and Unit Tests</a></li>
 </ul>
 <hr>
 <h3>
	 <?php print(esc_html__('Relase notes', 'github-updater')); ?>
	 -
	 <?php printf(esc_html__('see v. %s', 'github-updater'), $plugin_version); ?>
 </h3>
<pre id ="github-changes" style="white-space: pre-wrap; height:50ch; overflow-y:scroll;">
</pre>
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

<script>
	/**
	 * Dynamically retrieve GitHub opened issues,
	 * see: https://developer.github.com/v3/issues/
	 */
	$.ajax({
		url: 'https://api.github.com/repos/afragen/github-updater/issues?state=open',
	})
	.done(function(data) {
		// Blaclisted:
		// 339 = Travis-CI and Unit Tests;
		// 470 = Translations now using Language Pack updates;
		var blacklisted = [339, 470]; //TODO: use github labels instead..
		var ul = document.getElementById("github-issues");
		for (var i = 0; i < data.length; i++) {
			var issue = data[i];
			if( $.inArray( issue.number, blacklisted ) >= 0 ) {
				continue;
			}
			var li = document.createElement('li'),
					 a = document.createElement('a');
			a.href = issue.html_url;
			a.innerHTML = issue.title;
			li.appendChild(a);
			ul.appendChild(li);
		}
	})
	.fail(function() {
	 console.log("Failed to fetch data from GitHub API")
	})
	/**
	 * Dynamically retrieve GitHub release notes,
	 * see: https://developer.github.com/v3/contents/
	 */
	$.ajax({
		url: 'https://api.github.com/repos/afragen/github-updater/contents/CHANGES.md',
	})
	.done(function(data) {
		var pre = document.getElementById("github-changes");
		pre.innerHTML = atob(data.content);
	})
	.fail(function() {
	 console.log("Failed to fetch data from GitHub API")
	})
</script>
