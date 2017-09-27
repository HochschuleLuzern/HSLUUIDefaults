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
	    global $tpl;
		if ($a_comp == "Services/MainMenu" && $a_part == "main_menu_list_entries")
		{
			global $ilias;
			
			// On create group or folder with didactic templates
			if($this->specialMethodsExists() && isset($_GET['new_type']) && ($_GET['new_type']=='fold' || $_GET['new_type']=='grp') && isset($_GET['cmd']) && $_GET['cmd']=='create')
			{
			    $script = '';
			    
			    // On create folder
			    if($_GET['new_type']=='fold')
			    {
			        // In a fileexchange-folder -> only normal folders allowed (no didactic template)
			        if(ilDidacticTemplateObjSettings::isSpecialFolder($_GET['ref_id'], 'Dateiaustausch') ||
                       ilDidacticTemplateObjSettings::isInSpecialFolder($_GET['ref_id'], 'Dateiaustausch'))
			        {
			            $script = $this->hideDTPLsOnCreate(0);
			        }
			        // In a postbox-folder -> only postboxes allowed
			        else if(ilDidacticTemplateObjSettings::isSpecialFolder($_GET['ref_id'], 'Briefkasten'))
			        {
			            $script = $this->hideDTPLsOnCreate(ilDidacticTemplateObjSettings::lookupTemplateIdByName('Briefkasten'));
			        }
			    }
			    // On create group
			    else //if($_GET['new_type']=='grp')
			    {
			        // In a groupfolder -> only open groups allowed
			        if(ilDidacticTemplateObjSettings::isSpecialFolder($_GET['ref_id'], 'Gruppenordner'))
			        {
			            $script = $this->hideDTPLsOnCreate(0);
			        }
			        // Somewhere else -> Default is closed group
			        else
			        {
			            $script = $this->selectDTPLOnCreate(1);
			        }
			    }
			    $tpl->addOnLoadCode($script);
			}
		}

		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}
	
	/**
	 * Creates jquery-code to hide other didactic templates
	 * @param integer $allowed_dtpl_id ID of the only allowed didactic template
	 * @return string
	 */
	private function hideDTPLsOnCreate($allowed_dtpl_id)
	{
	    return '$("#didactic_type").children().each(function(index){
                  if(!$.contains(this, $("#didactic_type_dtpl_'.$allowed_dtpl_id.'")[0])){
                    $(this).hide();
                  }else{
                    $(this).find("input").attr("checked", true);
                  }
                });';
	}
	
	/**
	 * Creates jquery-code to select a default didactic template
	 * @param integer $select_dtpl_id ID of didactic template to select
	 * @return string
	 */
	private function selectDTPLOnCreate($select_dtpl_id)
	{
	    return '$("#didactic_type_dtpl_'.$select_dtpl_id.'").prop("checked", true);';
	}
	
	/**
	 * Checks if the needed methods from the hslu-patches exists
	 * @return boolean
	 */
	private function specialMethodsExists()
	{
	    $exist = method_exists('ilDidacticTemplateObjSettings', 'isSpecialFolder');
	    $exist &= method_exists('ilDidacticTemplateObjSettings', 'isInSpecialFolder');
	    $exist &= method_exists('ilDidacticTemplateObjSettings', 'lookupTemplateIdByName');
	    
	    return $exist;
	}
}
?>
