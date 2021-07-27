<?php
 
include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");
 
/**
 * HSLUUIDefaults plugin
 *
 * @author Simon Moor <simon.moor@hslu.ch>
 * @author Simon Moor <stephan.winiker@hslu.ch>
 * @version $Id$
 *
 */
class ilHSLUUIDefaultsPlugin extends ilUserInterfaceHookPlugin
{
    public function getPluginName()
    {
        return "HSLUUIDefaults";
    }
}
