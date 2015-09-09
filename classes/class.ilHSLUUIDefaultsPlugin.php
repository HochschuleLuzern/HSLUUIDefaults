<?php
 
include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");
 
/**
 * HSLUUIDefaults plugin
 *
 * @author Simon Moor <simon.moor@hslu.ch>
 * @version $Id$
 *
 */
class ilHSLUUIDefaultsPlugin extends ilUserInterfaceHookPlugin
{
        function getPluginName()
        {
                return "HSLUUIDefaults";
        }
}
 
?>