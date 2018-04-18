<?php
/**
 * GitHub Updater
 *
 * @package	GitHub_Updater
 * @author	Andy Fragen
 * @license	GPL-2.0+
 * @link	 https://github.com/afragen/github-updater
 */

use Fragen\GitHub_Updater\Model\JSON\GHUModelInterface;
use Fragen\GitHub_Updater\Model\JSON\GHUModel;
use Fragen\GitHub_Updater\Model\JSON\ComposerModel;
use Fragen\GitHub_Updater\WP_Dependency_Installer;

$action = add_query_arg( 'tab', $tab, $action );
?>

<style>
	/* div.json-beta-feature { display: none; } */
	.json-beta-feature button::after { content: " (beta) "; color: #f00; font-weight: 700; }
	pre.github-updater_json, pre.composer_json { padding:1em; background:#fff; border: 1px solid #ddd; }
	button.upload-json, button.delete-json { width: 100%; margin-top:1em; }
	button.download-json { width: 100%; }
	h3.json-alternative { width:100%; text-align:center; }
</style>

<?php
echo '<div class="wrap">';

// Readme.md link reminder
echo '<sub>Here you can Import/Export a "github-updater.json" file to be able to easily install plugins (here or somewhere else)</sub>';
echo '<br>';
echo '<sub>See <a href="https://github.com/afragen/github-updater" target="_blank">Readme</a> for more usage information</sub>';

$model = new GHUModel();
$model->fill();
$github_updater_json = $model->to_json();

$model = new ComposerModel();
$model->fill();
$composer_json = $model->to_json();

if(WP_Dependency_Installer::instance()->is_running_config()){
	echo '<button type="button" value="delete" id="delete-json_file" class="delete-json button button-secondary">Delete currently uploaded github-updater.json configuration file</button>';
	echo '<h3 class="json-alternative">or</h3>';
}

echo '<button type="button" value="upload" id="upload-json_file" class="upload-json button button-secondary">Upload a github-updater.json</button>';
echo '<input id="json_file" type="file" accept=".json,application/json" hidden/>';

echo '<h3 class="json-alternative">or</h3>';
echo '<button type="button" value="download" id="download-github-updater_json" class="download-json button button-secondary">Download the following github-updater.json</button>';

echo '<h4>github-updater.json</h4>';
echo '<pre class="github-updater_json">'. $github_updater_json .'</pre>';

echo '<div class="json-beta-feature">';
echo '<h3 class="json-alternative">or</h3>';
echo '<button type="button" value="download" id="download-composer_json" class="download-json button button-secondary">Download the following composer.json</button>';
echo '<sub style="float:right;">NB: actually wpackagist doesn\'t provide an easy way to check if a plugin is really present on their repo, please be patient..</sub>';

echo '<h4>composer.json</h4>';
echo '<pre class="composer_json">'. $composer_json .'</pre>';
echo '</div>';

echo '</div>';

?>
<script>
/**
 * Originally taken from: https://stackoverflow.com/questions/42266658/download-text-from-html-pre-tag
 */
function saveTextAsFile(e) {
	var btnID = e.target.id;																							// eg. download-github-updater_json
	var textClass = btnID.substring(btnID.indexOf('-')+1, btnID.length);	// eg. github-updater_json
	var textToWrite = document.querySelector('pre.'+textClass).innerText;	// eg. pre.github-updater_json
	var json_pos = textClass.lastIndexOf('_json');
	var fileName = textClass.substring(0,json_pos) + "" + textClass.substring(json_pos+5);

	var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
	var fileNameToSaveAs = fileName + ".json";

	var downloadLink = document.createElement("a");
	downloadLink.download = fileNameToSaveAs;
	downloadLink.innerHTML = "Download File";
	if (window.webkitURL != null) {
		// Chrome allows the link to be clicked without actually adding it to the DOM.
		downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
	} else {
		// Firefox requires the link to be added to the DOM before it can be clicked.
		downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
		downloadLink.onclick = function(){
			document.body.removeChild(downloadLink);
		};
		downloadLink.style.display = "none";
		document.body.appendChild(downloadLink);
	}
	downloadLink.click();
}

function openDialog() {
	document.getElementById('json_file').click();
}

function uploadJsonFile(e) {
	var input = e.target;
	if(input.files && input.files[0]){
		var reader = new FileReader();
		reader.onload = function(e){
				var json_object = JSON.parse(e.target.result);
				$.post(ajaxurl, {
					contentType: "application/json",
					action: 'dependency_installer',
					method: 'upload',
					//config  : e.target.result,
					config  : json_object,
					complete: function(data){
								var timeout = setTimeout("location.reload(true);",5000);
								alert("After you press OK, this page will automatically refresh when done.\nMaybe you may have to refresh this page a couple of times to see some changes.\n\nPlease be patient...");
							}
				});
		};
		reader.readAsText(e.target.files[0]);
	}
}
function deleteJsonFile(e) {
	$.post(ajaxurl, {
		action: 'dependency_installer',
		method: 'delete',
		complete: function(data){
			var timeout = setTimeout("location.reload(true);",5000);
			alert("After you press OK, this page will automatically refresh when done.\nMaybe you may have to refresh this page a couple of times to see some changes.\n\nPlease be patient...");
		}
		});
}

var input = document.getElementById('json_file');
input.addEventListener('change', uploadJsonFile);
var button00 = document.getElementById('delete-json_file');
if( typeof button00 !== "undefined" && button00 != null )
	button00.addEventListener('click', deleteJsonFile);
var button0 = document.getElementById('upload-json_file');
button0.addEventListener('click', openDialog);
var button1 = document.getElementById('download-github-updater_json');
button1.addEventListener('click', saveTextAsFile);
var button2 = document.getElementById('download-composer_json');
button2.addEventListener('click', saveTextAsFile);

</script>
