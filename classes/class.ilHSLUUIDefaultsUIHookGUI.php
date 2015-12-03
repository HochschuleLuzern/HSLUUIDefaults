<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
* ilHSLUUIDefaultsUIHookGUI class
*
* @author Simon Moor <simon.moor@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
*/
class ilHSLUUIDefaultsUIHookGUI extends ilUIHookPluginGUI {
	function getHTML($a_comp, $a_part, $a_par = array())
	{
		
		if ($a_comp == "Services/MainMenu" && $a_part == "main_menu_list_entries")
		{
			global $ilias;
			
			//gruppe erstellen default
			if(isset($_GET['new_type']) && $_GET['new_type']=='grp' && isset($_GET['cmd']) && $_GET['cmd']=='create'   ){
				return array("mode" => ilUIHookPluginGUI::APPEND, "html" =>
'
<script type="text/javascript">
il.Util.addOnLoad(function() {
	jQuery("#grp_type_1").click();
});
</script>
');
			}
			
			//mediacast create
			if(isset($_GET['cmdClass']) && $_GET['cmdClass']=='ilobjmediacastgui' && isset($_GET['cmd']) && $_GET['cmd']=='addCastItem'   ){
				return array("mode" => ilUIHookPluginGUI::APPEND, "html" =>
'

<script type="text/javascript" src="./Services/FileUpload/js/tmpl.js"></script>
<script type="text/javascript" src="./Services/FileUpload/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="./Services/FileUpload/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="./Services/FileUpload/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="./Services/FileUpload/js/jquery.ba-dotimeout.min.js"></script>
<script type="text/javascript" src="./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/HSLUUIDefaults/js/ilMediaCastUpload.js"></script>

<script type="text/javascript">
il.Util.addOnLoad(function() {
	jQuery("#il_center_col").prepend(\' \'
+\'<link rel="stylesheet" type="text/css" href="./Services/FileUpload/templates/default/fileupload.css" media="screen" />\'
+\' \'
+\' \'
+\'<form id="form2" role="form" class="form-horizontal preventDoubleSubmission" enctype="multipart/form-data" action="\'+jQuery("#form_").attr(\'action\')+\'" method="post"> \'
+\' \'
+\'<div class="form-horizontal"> \'
+\' \'
+\' \'
+\'		<div class="ilFormHeader"><h3 class="ilHeader"><a name="il_form_top"></a>Mehrere Mediacast-Beiträge hinzufügen</h3> \'
+\'		<div class="ilHeaderDesc"></div> \'
+\'		</div> \'
+\' \'
+\'<div class="form-group" id="il_prop_cont_title"> \'
+\'	<label for="title" class="col-sm-3 control-label">Dateien</label> \'
+\'	<div class="col-sm-9"> \'
+\'			 \'
+\'<div class="ilFileUploadContainer"> \'
+\'	<div id="ilFileUploadDropArea_1" class="ilFileUploadDropArea"> \'
+\'		<div id="ilFileUploadDropZone_1" class="ilFileUploadDropZone ilFileDropTarget"><i class="ilFileDropTargetOverlayImage"></i> Ziehen Sie die Dateien in diesen Bereich<br>&nbsp;<span class="ilFileUploadFileCount" style="display: none;">%s Datei(en) ausgewählt</span>&nbsp;</div> \'
+\'		<br>- oder - \'
+\'	</div> \'
+\'	<div class="ilFileUploadDropAlternative"> \'
+\'		<a id="ilFileUploadFileSelect_1" class="submit omitPreventDoubleSubmission" href="#"> \'
+\'			<span><img src="./Customizing/global/skin/hslu/images/icon_fold.svg"> Wählen Sie die Dateien von Ihrem Computer aus</span> \'
+\'            <input id="ilFileUploadInput_1" type="file" name="file_Standard" accept="" multiple=""> \'
+\'		</a> \'
+\'	</div> \'
+\'	<div id="ilFileUploadList_1" class="ilFileUploadList" style="display: none;"> \'
+\'		<div class="ilFileUploadListTitle">Ausgewählte Dateien: \'
+\'            <div class="ilFileUploadToggleOptions"> \'
+\'                <a class="ilFileUploadShowOptions" href="#">Alle Details anzeigen \'
+\'                </a> \'
+\'                <a class="ilFileUploadHideOptions" href="#" style="display: none;">Alle Details ausblenden \'
+\'                </a> \'
+\'            </div> \'
+\'		</div> \'
+\'	</div> \'
+\'	<div class="help-block">Maximal erlaubte Upload-Größe: 700.0 MB</div> \'
+\'</div> \'
+\'			 \'
+\'	</div> \'
+\'</div>  \'
+\' \'
+\'	\'
+\'	\'
+\'	<div class="ilFormFooter clearfix"> \'
+\'		<div class="col-sm-6 ilFormRequired">&nbsp;</div> \'
+\'		<div class="col-sm-6 ilFormCmds"> \'
+\'		<input class="btn btn-default btn-sm" type="button" name="cmd[uploadFiles]" value="Alle Dateien hochladen"> \'
+\'		<a id="goBackToMediacast" href="ilias.php?baseClass=ilMediaCastHandlerGUI&cmd=listItems&ref_id='.(int)$_GET['ref_id'].'"> </a> \'
+\'		</div> \'
+\'	</div> \'
+\'	\'
+\'</div>\'
+\'</form>\'
+\'	\'
);



fileUpload1 = new ilFileUpload(1, {"dropZone":"#ilFileUploadDropZone_1","fileInput":"#ilFileUploadInput_1","submitButton":"uploadFiles","cancelButton":"cancel","dropArea":"#ilFileUploadDropArea_1","fileList":"#ilFileUploadList_1","fileSelectButton":"#ilFileUploadFileSelect_1"});

});
</script>
<script type="text/javascript">
    il.FileUpload.texts.fileTooLarge = "Die hochzuladende Datei überschreitet die maximale Dateigröße.";
    il.FileUpload.texts.invalidFileType = "Falscher Dateityp";
    il.FileUpload.texts.fileZeroBytes = "Die Datei ist entweder 0 Byte gross oder es handelt sich um einen Ordner.";
    il.FileUpload.texts.uploadWasZeroBytes = "Das Hochladen ist fehlgeschlagen, da dies entweder ein Ordner ist, die Datei 0 Byte gross ist oder die Datei inzwischen gelöscht oder umbenannt wurde.";
    il.FileUpload.texts.cancelAllQuestion = "Wollen Sie das Hochladen der ausstehenden Dateien abbrechen?";
    il.FileUpload.texts.extractionFailed = "Das Entpacken des Archivs und dessen Ordnerstruktur ist fehlgeschlagen. Wahrscheinlich verfügen Sie nicht über die Rechte um Ordner oder Kategorien in diesem Objekt zu erstellen.";
    il.FileUpload.texts.uploading = "Hochladen...";
    il.FileUpload.texts.extracting = "Entpacken...";
    il.FileUpload.texts.dropFilesHere = "Ziehen Sie die Dateien hier hin, um sie in dieses Objekt hochzuladen.";
    il.FileUpload.defaults.concurrentUploads = 3;
    il.FileUpload.defaults.maxFileSize = 734003200;
    il.FileUpload.defaults.allowedExtensions = [ "mp4","m4v","mov","flv","wmv","avi","mts","m2ts","mov","avi","wmv","aac","rm","mpg","mpeg","divx","flv","swf","ts","vob","mkv","ogv","mjpeg","m4v","3gpp","mp3","png","jpg","gif" ];
    il.FileUpload.defaults.supportedArchives = [];
	
	function removeMediaCastFile(id){
		$(id).fadeOut().remove();
		return false;
	}
	
</script>

<script type="text/x-tmpl" id="fileupload_row_tmpl">
<table id="{%=o.id%}" class="ilFileUploadEntry {% o.canUpload ? print(\'\') : print(\'ilFileUploadNoUpload\'); %}" datatable="0">
	<tbody>
		<tr class="ilFileUploadEntryHeader">
			<td class="ilFileUploadEntryFileName">
				<span class="ilFileUploadEntryError"><span class="ilFileUploadEntryErrorText"></span><br /></span>{%=o.name%}
			</td>
			<td class="ilFileUploadEntryProgress">
				<span class="ilFileUploadEntryProgressBack">
					&nbsp;
					<span class="ilFileUploadEntryProgressBar"></span>
					<span class="ilFileUploadEntryProgressPercent">{% (o.size === null && o.canUpload) ? print("Ausstehend") : print(o.sizeFormatted); %}</span>
				</span>
			</td>
			<td class="ilFileUploadEntryCancel"><button id="cancel_{%=o.id%}" class="btn btn-link" src="" ><span class="glyphicon glyphicon-remove"></span></button></td>
		</tr>
		{% if (o.canUpload) { %}
		<tr class="ilFileUploadEntryOptions">
			<td colspan="3">
				<div class="ilFileUploadEntryTitle form-group">
					<label class="col-sm-3 control-label" for="title_{%=o.id%}">Titel</label>					
					<div class="col-sm-9">
						<input type="text" class="form-control" size="40" id="title_{%=o.id%}" maxlength="128" name="title" value="{%=o.name%}">
					</div>
				</div>
				<div  class="ilFileUploadEntryDescription form-group">
					<label class="col-sm-3 control-label" for="desc_{%=o.id%}">Beschreibung</label>					
					<div class="col-sm-9">
						<textarea name="description" class="form-control" id="desc_{%=o.id%}" class="noRTEditor" wrap="virtual" rows="" style="width:90%;"></textarea>
					</div>
				</div>	
				<div  class="ilFileUploadEntryDescription form-group">
					<label class="col-sm-3 control-label" for="visibility_{%=o.id%}">Zugriff <span class="asterisk">*</span></label>					
					<div class="col-sm-9">
						<label>
							<input type="radio" id="visibility_{%=o.id%}" name="visibility_{%=o.id%}" value="users">
							Eingeloggte Benutzer</label>
						<label>
							<input type="radio" id="visibility_{%=o.id%}" name="visibility_{%=o.id%}" value="public" checked="checked">
							Öffentlich
						</label>
					</div>
				</div>				
			</td>
		</tr>
		{% } %}
	</tbody>
</table>
</script>
');
			}
			
			
		}

		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}
}
?>