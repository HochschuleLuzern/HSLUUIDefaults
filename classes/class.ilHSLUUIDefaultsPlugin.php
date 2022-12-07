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
    public function __construct()
    {
        parent::__construct();
        /*
         * We don't want this to be executed on the commandline, as it makes the setup fail
         */
        if (php_sapi_name() === 'cli') {
            return;
        }
        
        global $DIC;
        
        $this->provider_collection
        ->setModificationProvider(new ilHSLUUIDefaultsGlobalScreenModificationProvider($DIC, $this));

        $this->provider_collection
        ->setMainBarProvider(new ilHSLUUIDefaultsGlobalScreenMenuProvider($DIC, $this));
    }
    
    public function getPluginName()
    {
        return "HSLUUIDefaults";
    }
}
