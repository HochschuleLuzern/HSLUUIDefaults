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

    /**
     * Send Info Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */

    public static function sendInfo($a_info = "", $a_keep = false)
    {
        global $DIC;

        if(isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("info", $a_info, $a_keep);
        }
    }

    /**
     * Send Failure Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */

    public static function sendFailure($a_info = "", $a_keep = false)
    {
        global $DIC;

        if (isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("failure", $a_info, $a_keep);
        }
    }

    /**
     * Send Question to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static	*/
    public static function sendQuestion($a_info = "", $a_keep = false)
    {
        global $DIC;

        if(isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("question", $a_info, $a_keep);
        }
    }

    /**
     * Send Success Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendSuccess($a_info = "", $a_keep = false)
    {
        global $DIC;

        /** @var ilTemplate $tpl */
        if(isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("success", $a_info, $a_keep);
        }
    }
}
