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
				
				global $lng;
				require_once("./Services/FileUpload/classes/class.ilFileUploadSettings.php");

				include_once ("./Modules/MediaCast/classes/class.ilMediaCastSettings.php");
				$settings = ilMediaCastSettings::_getInstance();
				$purposeSuffixes = $settings->getPurposeSuffixes();
				
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
+\'		<div class="ilFormHeader"><h3 class="ilHeader"><a name="il_form_top"></a>'.$lng->txt('mcst_add_new_item').'</h3> \'
+\'		<div class="ilHeaderDesc"></div> \'
+\'		</div> \'
+\' \'
+\'<div class="form-group" id="il_prop_cont_title"> \'
+\'	<label for="title" class="col-sm-3 control-label">Dateien</label> \'
+\'	<div class="col-sm-9"> \'
+\'			 \'
+\'<div class="ilFileUploadContainer"> \'
+\'	<div id="ilFileUploadDropArea_1" class="ilFileUploadDropArea"> \'
+\'		<div id="ilFileUploadDropZone_1" class="ilFileUploadDropZone ilFileDropTarget"><i class="ilFileDropTargetOverlayImage"></i> '.$lng->txt('drag_files_here').'<br>&nbsp;<span class="ilFileUploadFileCount" style="display: none;">'.$lng->txt('num_of_selected_files').'</span>&nbsp;</div> \'
+\'		<br>- '.$lng->txt('logic_or').' - \'
+\'	</div> \'
+\'	<div class="ilFileUploadDropAlternative"> \'
+\'		<a id="ilFileUploadFileSelect_1" class="submit omitPreventDoubleSubmission" href="#"> \'
+\'			<span><img src="./templates/default/images/icon_fold.svg"> '.$lng->txt('select_files_from_computer').'</span> \'
+\'            <input id="ilFileUploadInput_1" type="file" name="file_Standard" accept="" multiple=""> \'
+\'		</a> \'
+\'	</div> \'
+\'	<div id="ilFileUploadList_1" class="ilFileUploadList" style="display: none;"> \'
+\'		<div class="ilFileUploadListTitle">'.$lng->txt('selected_files').': \'
+\'            <div class="ilFileUploadToggleOptions"> \'
+\'                <a class="ilFileUploadShowOptions" href="#">'.$lng->txt('show_all_details').' \'
+\'                </a> \'
+\'                <a class="ilFileUploadHideOptions" href="#" style="display: none;">'.$lng->txt('hide_all_details').' \'
+\'                </a> \'
+\'            </div> \'
+\'		</div> \'
+\'	</div> \'
+\'	<div class="help-block">'.$lng->txt('file_notice').': 700.0 MB</div> \'
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
+\'		<input class="btn btn-default btn-sm" type="button" name="cmd[uploadFiles]" value="'.$lng->txt('upload_files').'"> \'
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
    il.FileUpload.texts.fileTooLarge = "'.$lng->txt("form_msg_file_size_exceeds").'";
    il.FileUpload.texts.invalidFileType = "'.$lng->txt("form_msg_file_wrong_file_type").'";
    il.FileUpload.texts.fileZeroBytes = "'.$lng->txt("error_empty_file_or_folder").'";
    il.FileUpload.texts.uploadWasZeroBytes = "'.$lng->txt("error_upload_was_zero_bytes").'";
    il.FileUpload.texts.cancelAllQuestion = "'.$lng->txt("cancel_file_upload").'";
    il.FileUpload.texts.extractionFailed = "'.$lng->txt("error_extraction_failed").'";
    il.FileUpload.texts.uploading = "'.$lng->txt("uploading").'";
    il.FileUpload.texts.extracting = "'.$lng->txt("extracting").'";
    il.FileUpload.texts.dropFilesHere = "'.$lng->txt("drop_files_on_repo_obj_info").'";
    il.FileUpload.defaults.concurrentUploads = '.ilFileUploadSettings::getConcurrentUploads().';
    il.FileUpload.defaults.maxFileSize = '.ilFileUploadUtil::getMaxFileSize().';
    il.FileUpload.defaults.allowedExtensions = [ "'.implode('","',$purposeSuffixes['Standard']).'" ];
    il.FileUpload.defaults.supportedArchives = [];
	
	function removeMediaCastFile(id){
		$(id).fadeOut().remove();
		return false;
	}
	function MediaCastFileTitle(filename){
		
		for (index = 0, len = il.FileUpload.defaults.allowedExtensions.length; index < len; ++index) {
			ending=filename.substr(filename.length-(il.FileUpload.defaults.allowedExtensions[index].length));
			if(ending==il.FileUpload.defaults.allowedExtensions[index]){
				return filename.substr(0,filename.length-ending.length-1);
			}
		}
		return filename;
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
					<span class="ilFileUploadEntryProgressPercent">{% (o.size === null && o.canUpload) ? print("'.$lng->txt("upload_pending").'") : print(o.sizeFormatted); %}</span>
				</span>
			</td>
			<td class="ilFileUploadEntryCancel"><button id="cancel_{%=o.id%}" class="btn btn-link" src="" ><span class="glyphicon glyphicon-remove"></span></button></td>
		</tr>
		{% if (o.canUpload) { %}
		<tr class="ilFileUploadEntryOptions">
			<td colspan="3">
				<div class="ilFileUploadEntryTitle form-group">
					<label class="col-sm-3 control-label" for="title_{%=o.id%}">'.$lng->txt("title").'</label>					
					<div class="col-sm-9">
						<input type="text" class="form-control" size="40" id="title_{%=o.id%}" maxlength="128" name="title" value="{%=MediaCastFileTitle(o.name)%}">
					</div>
				</div>
				<div  class="ilFileUploadEntryDescription form-group">
					<label class="col-sm-3 control-label" for="desc_{%=o.id%}">'.$lng->txt("description").'</label>					
					<div class="col-sm-9">
						<textarea name="description" class="form-control" id="desc_{%=o.id%}" class="noRTEditor" wrap="virtual" rows="" style="width:90%;"></textarea>
					</div>
				</div>	
				<div  class="ilFileUploadEntryDescription form-group">
					<label class="col-sm-3 control-label" for="visibility_{%=o.id%}">'.$lng->txt("access").' <span class="asterisk">*</span></label>					
					<div class="col-sm-9">
						<label>
							<input type="radio" id="visibility_{%=o.id%}" name="visibility_{%=o.id%}" value="users">
							'.$lng->txt("access_users").'</label>
						<label>
							<input type="radio" id="visibility_{%=o.id%}" name="visibility_{%=o.id%}" value="public" checked="checked">
							'.$lng->txt("access_public").'
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
