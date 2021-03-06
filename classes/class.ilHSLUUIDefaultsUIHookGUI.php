<?php
/**
* ilHSLUUIDefaultsUIHookGUI class
*
* @author Stephan Winiker <stephan.winiker@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
*/
class ilHSLUUIDefaultsUIHookGUI extends ilUIHookPluginGUI {
	private $ctrl;
	private $tree;
	private $lang;
	private $tabs;
	private $user;
	private $ref_id;
	private $obj_def;
	
	private $rbacsystem;
	
	private $obj_types_with_backlinks = ['blog','book','cat', 'copa', 'crs','dbk','dcl','exc','file','fold','frm','glo','grp','htlm', 'lso', 'mcst','mep','qpl','sahs','svy','tst','webr','wiki','xavc','xlvo','xmst','xpdl','xstr','xvid'];

	protected function buildSelectAllCheckbox() {
		global $DIC;

		$select_all_str = $DIC->language()->txt("select_all", "Select All");
		return "<tr><td><input class='selectall' type='checkBox' onclick='checkAllCheckboxes(this)' /><span style='margin-left: 10px'>$select_all_str</span></td></tr>";
	}

	
	function getHTML($a_comp, $a_part, $a_par = array())
	{
		if ($a_comp == "Services/PersonalDesktop" && $a_par == "left_column") {
			return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
		}

		// This is just a placeholder to inject some JavaScript in the main html
		else if($a_comp == "Services/MainMenu" && $a_part == "main_menu_search") {
			$html = "";

		   //Set groups to closed by default
		   if (isset($_GET['new_type']) && $_GET['new_type']=='grp' && isset($_GET['cmd']) && $_GET['cmd']=='create'){
		       $html .= '<script>il.Util.addOnLoad(function() {jQuery("#didactic_type_dtpl_1").click();});</script>';
		   }

		   // Create Checkbox to select all content
			if (isset($_GET["cmdClass"]) && isset($_GET["baseClass"]) && isset($_GET["cmd"])
				&& strtolower($_GET["cmdClass"]) == 'ilpdselecteditemsblockgui' && strtolower($_GET["baseClass"]) == 'ildashboardgui' && strtolower($_GET["cmd"]) == 'manage') {

				$checkbox_html = $this->buildSelectAllCheckbox();

				// JavaScript to add a checkbox. The var $checkbox_html is used 1 time inside of this js-Block to make the code a little bit more readable
				$html .= '<script>
						function checkAllCheckboxes(obj) {
							let checked = obj.checked;
							par = obj.parentElement.parentElement.parentElement;
							par.querySelectorAll("input[type=\'checkbox\']").forEach(
								box => { box.checked = checked; }
							);
						}
						
						il.Util.addOnLoad(function() {
							document.querySelectorAll(".table-responsive table tbody").forEach((obj) => { 
								let checkbox = document.createElement("template");
								checkbox.innerHTML = "'.$checkbox_html.'";
								obj.append(checkbox.content.firstChild);
							 });
						});
					</script>';
			}

		   return array("mode" => ilUIHookPluginGUI::APPEND, "html" => $html);
		}
	}
	
	function modifyGUI($a_comp, $a_part, $a_par = array())
	{
		if ($a_part == "tabs")
		{
			global $DIC;
			$this->ctrl = $DIC->ctrl();
			$this->tree = $DIC->repositoryTree();
			$this->lang = $DIC->language();
			$this->tabs = $DIC->tabs();
			$this->user = $DIC->user();
			$this->obj_def = $DIC["objDefinition"];
			$this->rbacsystem = $DIC->rbac()->system();
			
			$this->ref_id=(int)$_GET['ref_id'];
			
			$classes = [];
			
			foreach ($this->ctrl->getCallHistory() as $call) {
				$classes [] = $call['class'];
			}
			
			if($_GET['baseClass']=='ilPersonalDesktopGUI' && ((int)$_GET['wsp_id']!=0) 
					|| array_search('ilObjRoleGUI', $classes) !== false
					|| $this->ref_id==0)
			{
				//We are in the Personal Desktop, in the root note, or in the roleGUI and we do nothing
			}
			else if($_GET['baseClass']=='ilMailGUI' && ((int)$_GET['mail_id']!=0) || $_GET['cmd']=='mailUser' || $_GET['cmdClass']=='ilmailformgui' || $_GET['ref']=='mail')
			{
				//We are in emails and simply set a fixed back link
				$a_par["tabs"]->setBackTarget($this->lang->txt("back"),'ilias.php?cmdClass=ilmailfoldergui&baseClass=ilMailGUI');
			}
			else
			{
				$this->addTrashLink();
				$this->addBacklink($a_par);
			}
		}
	}
	
	private function addTrashLInk()
	{
		if($this->user->login=='anonymous')
		{
		
		}
		else if($this->rbacsystem->checkAccess('create_file',$this->ref_id))
		{
			
			$objects = $this->tree->getSavedNodeData($this->ref_id);
			if (count($objects) > 0)
			{
				$obj_type=$this->ctrl->context_obj_type;
				$class_name = $this->obj_def->getClassName($obj_type);
				$next_class = strtolower("ilObj".$class_name."GUI");
				
				if($next_class=='ilobjgui')return;
				$objectgui = new $next_class("", $this->ref_id, true, false);
				if($objectgui!=null)
				{
					$this->tabs->addTab("trash",$this->lang->txt('trash'), $this->ctrl->getLinkTarget($objectgui, "trash"));
				}
			}
		}
	}
	
	private function addBacklink($a_par)
	{	
		$parent_id=$this->tree->getParentId($this->ref_id);
		$object=ilObjectFactory::getInstanceByRefId($this->ref_id, false);
		
		if ($object) {
			$obj_type=$object->getType();
		} else {
			$obj_type = "";
		}
		
		if(count($a_par["tabs"]->target)>0 AND in_array($obj_type,$this->obj_types_with_backlinks))
		{
			// This function only works with a hslu-patch
			if(!method_exists($this->tabs,'hasBackTarget') || !$this->tabs->hasBackTarget())
			{			
				$parentobject=ilObjectFactory::getInstanceByRefId($parent_id);
				$parent_type = $parentobject->getType();
				
				if($obj_type == 'crs' || ($obj_type == 'grp' && ($parent_type == 'cat' || $parent_type == 'root')))
				{
					$favorite_link = $this->ctrl->getLinkTargetByClass('ilDashboardGUI', 'show');
					$this->tabs->setBackTarget($this->plugin_object->txt('favorite_link'), $favorite_link);
				}
				else
				{
					$explorer = new ilRepositoryExplorer($parent_id);
					$back_link = $explorer->buildLinkTarget($parent_id, $parent_type);
					$this->tabs->setBackTarget($this->plugin_object->txt("back_link"),$back_link);
				}
			}
		}
	}
}
?>
