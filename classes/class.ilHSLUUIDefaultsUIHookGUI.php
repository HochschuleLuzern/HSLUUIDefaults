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
		}

		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}
}
?>
