<?php

use ILIAS\UI\Renderer;
use ILIAS\DI\UIServices;
use Psr\Http\Message\RequestInterface;

class ilHSLUUIDefaultsConfigGUI extends ilPluginConfigGUI {
	private \ilHSLUUIDefaultsConfig $config;
    private UIServices $ui;
    private RequestInterface $request;
    private \ilCtrl $ctrl;
	
	function performCommand($cmd) {
		switch ($cmd)
		{
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

	public function configure() {
		$form = $this->initConfigurationForm();
		$this->ui->mainTemplate()->setContent($this->ui->renderer()->render($form));
	}
	
	private function initConfigurationForm() {
	    $categories_with_fav_link = $this->config->getCategoriesWithFavLinkAsString();
	    $categories_with_fav_link_input = $this->ui->factory()->input()->field()->text('categories_with_fav_link')->withValue($categories_with_fav_link);
		$form_actions = $this->ctrl->getFormActionByClass('ilHSLUUIDefaultsConfigGUI', 'save');
		return $this->ui->factory()->input()->container()->form()->standard($form_actions, 
		    ['categories_with_fav_link' => $categories_with_fav_link_input]);
	}
	
	private function save() {
		$form = $this->initConfigurationForm();
		$form = $form->withRequest($this->request);
		$success = $this->config->saveConfig($form->getData());
		if ($success < 0) {
		    ilUtil::sendFailure($this->pl->txt("save_failure"), true);
		} else if ($success == 0) {
		    ilUtil::sendInfo($this->pl->txt("nothing_changed"), true);
		} else {
		    ilUtil::sendSuccess($this->pl->txt("save_success"), true);
		}
		$this->configure();
	}
}
?>
