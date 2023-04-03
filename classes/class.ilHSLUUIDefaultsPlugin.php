<?php declare(strict_types = 1);
 
include_once "class.ilHSLUUIDefaultsGlobalScreenModificationProvider.php";
 
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
    const ID = "hsluuidef";

    public function __construct()
    {

        global $DIC;
        $this->db = $DIC->database();
        parent::__construct($this->db, $DIC["component.repository"], self::ID);

        /*
         * We don't want this to be executed on the commandline, as it makes the setup fail
         */
        if (php_sapi_name() === 'cli') {
            return;
        }
        
        global $DIC;
        
        $this->provider_collection
        ->setModificationProvider(new ilHSLUUIDefaultsGlobalScreenModificationProvider($DIC, $this));
    }
    
    public function getPluginName(): string
    {
        return "HSLUUIDefaults";
    }
}
