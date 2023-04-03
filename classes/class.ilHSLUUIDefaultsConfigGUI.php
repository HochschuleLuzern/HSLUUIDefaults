<?php declare(strict_types = 1);
/**
 * HSLUUIDefaults configuration user interface class
 *
 * @author  Mark Salter <mark.salterr@hslu.ch>
 * @version $Id$
 * @ilCtrl_isCalledBy    ilHSLUUIDefaultsConfigGUI: ilObjComponentSettingsGUI
 */
use ILIAS\UI\Component\Input\Container\Form\Standard as StandardForm;
use ILIAS\DI\UIServices;
use Psr\Http\Message\RequestInterface;

class ilHSLUUIDefaultsConfigGUI extends ilPluginConfigGUI
{
    private ilHSLUUIDefaultsConfig $config;
    private UIServices $ui;
    private RequestInterface $request;
    private ilCtrl $ctrl;
    
    public function performCommand($cmd) : void
    {
        switch ($cmd) {
            case "configure":
            case "save":
                $this->pl = $this->getPluginObject();
                global $DIC;
                $this->ctrl = $DIC->ctrl();
                $this->ui = $DIC->ui();
                $this->request = $DIC->http()->request();
                $this->config = new ilHSLUUIDefaultsConfig($DIC->database());
                $this->$cmd();
                break;

        }
    }

    public function configure() : void
    {
        $form = $this->initConfigurationForm();
        $this->ui->mainTemplate()->setContent($this->ui->renderer()->render($form));
    }
    
    private function initConfigurationForm() : StandardForm
    {
        $categories_with_fav_link = $this->config->getCategoriesWithFavLinkAsString();
        $categories_with_fav_link_input = $this->ui->factory()->input()->field()->text($this->plugin_object->txt('categories_with_fav_link'))
        ->withByline($this->plugin_object->txt('categories_with_fav_link_desc'))
        ->withValue($categories_with_fav_link);
        $form_actions = $this->ctrl->getFormActionByClass('ilHSLUUIDefaultsConfigGUI', 'save');
        return $this->ui->factory()->input()->container()->form()->standard(
            $form_actions,
            ['categories_with_fav_link' => $categories_with_fav_link_input]
        );
    }
    
    private function save() : void
    {
        $form = $this->initConfigurationForm();
        $form = $form->withRequest($this->request);
        $success = $this->config->saveConfig($form->getData());
        if ($success < 0) {
            ilUtil::sendFailure($this->pl->txt("save_failure"), true);
        } elseif ($success == 0) {
            ilUtil::sendInfo($this->pl->txt("nothing_changed"), true);
        } else {
            ilUtil::sendSuccess($this->pl->txt("save_success"), true);
        }
        $this->configure();
    }
}
