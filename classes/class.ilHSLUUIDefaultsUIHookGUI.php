<?php declare(strict_types = 1);

/**
 * @author Raphael Heer <mark.salter@hslu.ch>
 * @version $Id$
 * @ilCtrl_IsCalledBy ilHSLUUIDefaultsUIHookGUI: ilRepositoryGUI, ilObjTestGUI
 */
class ilHSLUUIDefaultsUIHookGUI extends ilUIHookPluginGUI
{
    private ilCtrl $ctrl;
    private ilTree $tree;
    private ilLanguage $lang;
    private ilTabsGUI $tabs;
    private ilObjUser $user;
    private int $ref_id;
    private ilObjectDefinition $obj_def;
    private ilRbacSystem $rbacsystem;
    private array $obj_types_with_backlinks;
    private array $container_types_with_favlinks;
    private array $categories_with_fav_link;
    
    public function modifyGUI($a_comp, $a_part, $a_par = array()) : void
    {
        if ($a_part == "tabs") {
            try {

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
                $this->container_types_with_favlinks = $config->getContainerTypesWithFavLinks();
                $this->obj_types_with_backlinks = $config->getObjTypesWithBacklinks();

                $this->ref_id = (int) $_GET['ref_id'];

                $classes = [];

                foreach ($this->ctrl->getCallHistory() as $call) {
                    if (array_key_exists('class',$call)){
                        $classes[] = strtolower($call['class']);
                    }
                }

                $base_class = strtolower($_GET['baseClass'] ?? '');
                $cmd_class = strtolower($_GET['cmdClass'] ?? '');
                $cmd = strtolower($_GET['cmd'] ?? '');
                $ref = strtolower($_GET['ref'] ?? '');
                $wsp_id = (int) $_GET['wsp_id'];
                $mail_id = (int) $_GET['mail_id'];

                //Catch all cases we don't want to change anything
                if ($base_class === 'ilpersonaldesktopgui' && $wsp_id !== 0 ||
                    $cmd === 'edit' && $base_class !== 'ilrepositorygui' ||
                    $cmd === 'editquestion' ||
                    $base_class === 'ilrepositorygui' && $cmd === 'create' && isset($_GET['new_type']) ||
                    in_array($cmd_class, $config->getCmdClassesWithoutChanges()) ||
                    array_search('ilobjrolegui', $classes) !== false ||
                    $this->ref_id === 0) {
                    return;
                }

                //We are in emails and simply set a fixed back link
                if ($base_class === 'ilmailgui' && ($mail_id !== 0) ||
                    $cmd === 'mailuser' ||
                    $cmd_class === 'ilmailformgui' ||
                    $ref === 'mail') {
                    $a_par["tabs"]->setBackTarget($this->lang->txt("back"), 'ilias.php?cmdClass=ilmailfoldergui&baseClass=ilMailGUI');
                    return;
                }

                $this->addTrashLink();
                $this->addBacklink($a_par);
            } catch (Exception $e) {
                // Log error -> generating a backlink or trashlink should not make ILIAS crash
                global $DIC;
                $DIC->logger()->root()->log('Exception in ilHSLUUIHDefaults-Plugin: ' . $e->getMessage() . $e->getTraceAsString());
            }
        }
    }
    
    private function addTrashLink() : void
    {
        if ($this->user->login != 'anonymous' && $this->rbacsystem->checkAccess('create_file', $this->ref_id)) {
            $objects = $this->tree->getSavedNodeData($this->ref_id);
            if (count($objects) > 0) {
                $obj_type = $this->ctrl->getContextObjType();
                if ($obj_type == null) {
                    return;
                }
                $class_name = $this->obj_def->getClassName($obj_type);
                $next_class = "ilObj" . $class_name . "GUI";
                
                if (strtolower($next_class) == 'ilobjgui') {
                    return;
                }
                $objectgui = new $next_class("", $this->ref_id, true, false);
                if ($objectgui != null) {
                    $this->tabs->addTab("trash", $this->lang->txt('trash'), $this->ctrl->getLinkTarget($objectgui, "trash"));
                }
            }
        }
    }
    
    private function addBacklink(array $a_par) : void
    {
        $parent_id = $this->tree->getParentId($this->ref_id);
        $object = ilObjectFactory::getInstanceByRefId($this->ref_id, false);
        
        if ($object) {
            $obj_type = $object->getType();
        } else {
            $obj_type = "";
        }
        
        if (count($a_par["tabs"]->target) > 0 and in_array($obj_type, $this->obj_types_with_backlinks)) {
            // This function only works with a hslu-patch
            if (!method_exists($this->tabs, 'hasBackTarget') || !$this->tabs->hasBackTarget()) {
                $parentobject = ilObjectFactory::getInstanceByRefId($parent_id);
                $parent_type = $parentobject->getType();
                
                if (in_array($obj_type, $this->container_types_with_favlinks) ||
                in_array($this->ref_id, $this->categories_with_fav_link)) {
                    $favorite_link = $this->ctrl->getLinkTargetByClass('ilDashboardGUI', 'show');
                    $this->tabs->setBackTarget($this->plugin_object->txt('favorite_link'), $favorite_link);
                } else {
                    $back_link = ilLink::_getLink($parent_id);

                    if ($parent_type == 'xcwi') {
                        $this->tabs->setBackTarget($this->plugin_object->txt("xcwi_back_link"), $back_link);
                    } else {
                        $this->tabs->setBackTarget($this->plugin_object->txt("back_link"), $back_link);
                    }
                }
            }
        }
    }
}
