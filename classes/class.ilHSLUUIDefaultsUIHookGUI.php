<?php
declare(strict_types = 1);

class ilHSLUUIDefaultsUIHookGUI extends ilUIHookPluginGUI {
	private \ilCtrl $ctrl;
	private \ilTree $tree;
	private \ilLanguage $lang;
	private \ilTabsGUI $tabs;
	private \ilObjUser $user;
	private $ref_id;
	private $obj_def;
	private $categories_with_fav_link;
	
	private \ilRbacSystem $rbacsystem;
	
	private array $obj_types_with_backlinks = ['blog','book','cat', 'copa', 'crs','dbk','dcl','exc','file','fold','frm','glo','grp','htlm', 'lso', 'mcst','mep','qpl','sahs','svy','tst','webr','wiki','xavc','xlvo','xmst','xpdl','xstr','xvid'];

	
	function getHTML($a_comp, $a_part, $a_par = array())
	{
	    if ($a_comp == "Services/MainMenu" && $a_part == "main_menu_list_entries") {
	        //Set groups to closed by default
	        if (isset($_GET['new_type']) && $_GET['new_type']=='grp' && isset($_GET['cmd']) && $_GET['cmd']=='create'){ 
	            $html .= '<script>il.Util.addOnLoad(function() {jQuery("#didactic_type_dtpl_1").click();});</script>';
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
			
			$config = new ilHSLUUIDefaultsConfig($DIC->database());
			$this->categories_with_fav_link = $config->getCategoriesWithFavLink();
			
			$this->ref_id=(int)$_GET['ref_id'];
			
			$classes = [];
			
			foreach ($this->ctrl->getCallHistory() as $call) {
				$classes [] = $call['class'];
			}
			
			if($_GET['baseClass']=='ilPersonalDesktopGUI' && ((int)$_GET['wsp_id']!=0)
			        || ($_GET['cmd']=='edit' && $_GET['baseClass'] != 'ilrepositorygui')
					|| array_search('ilObjRoleGUI', $classes) !== false
					|| $this->ref_id==0)
			{
				//We are in the Personal Desktop, in the root note, in the editor or in the roleGUI and we do nothing
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
	
	private function addTrashLink()
	{
		if ($this->user->login != 'anonymous' && $this->rbacsystem->checkAccess('create_file',$this->ref_id)) {
			
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
	
	private function addBacklink($a_par) {	
		$parent_id=$this->tree->getParentId($this->ref_id);
		$object=ilObjectFactory::getInstanceByRefId($this->ref_id, false);
		
		if ($object) {
			$obj_type=$object->getType();
		} else {
			$obj_type = "";
		}
		
		if(count($a_par["tabs"]->target) > 0 AND in_array( $obj_type, $this->obj_types_with_backlinks)) {
			// This function only works with a hslu-patch
			if(!method_exists($this->tabs,'hasBackTarget') || !$this->tabs->hasBackTarget()) {			
				$parentobject=ilObjectFactory::getInstanceByRefId($parent_id);
				$parent_type = $parentobject->getType();
				
				if(($obj_type == 'crs' || $obj_type == 'grp') && ($parent_type == 'cat' || $parent_type == 'root'))
				if((($obj_type == 'crs' || 
				    $obj_type == 'grp') && ($parent_type == 'cat' || $parent_type == 'root')) ||
				    in_array($this->ref_id, $this->categories_with_fav_link)) {
					$favorite_link = $this->ctrl->getLinkTargetByClass('ilDashboardGUI', 'show');
					$this->tabs->setBackTarget($this->plugin_object->txt('favorite_link'), $favorite_link);
				}
				else
				{
					$explorer = new ilRepositoryExplorer($parent_id);
					$back_link = $explorer->buildLinkTarget($parent_id, $parent_type);
					if($parent_type == 'xcwi') {
					    $this->tabs->setBackTarget($this->plugin_object->txt("xcwi_back_link"),$back_link);
					} else {
					   $this->tabs->setBackTarget($this->plugin_object->txt("back_link"),$back_link);
					}
				}
			}
		}
	}
}
?>
