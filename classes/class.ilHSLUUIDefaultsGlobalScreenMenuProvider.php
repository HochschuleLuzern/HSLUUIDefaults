<?php declare(strict_types=1);

class ilHSLUUIDefaultsGlobalScreenMenuProvider extends ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticMainMenuPluginProvider
{

    public function getStaticTopItems() : array
    {
        return [];
    }

    public function getStaticSubItems() : array
    {
        // Save needed factories in this variables to make the code more readable
        $menu_item_factory = $this->dic->globalScreen()->mainBar();
        $id_provider_factory = $this->dic->globalScreen()->identification()->plugin($this->getPluginID(), $this);

        // Some needed classes from the Dependency Injection Container (DIC)
        $ctrl = $this->dic->ctrl();
        $user = $this->dic->user();
        $access_checker = new ilHSLUUIDefaultsAccessChecker($this->dic->rbac()->review());

        // Menu items we want to provide
        $menu_items = [];

        // Build URL for the dashboard page in 'ilHSLUUIDefaultsCtrlRoutingGUI'
        $link = $ctrl->getLinkTargetByClass(ilHSLUUIDefaultsCtrlRoutingGUI::CTRL_UI_ROUTE, ilHSLUUIDefaultsCtrlRoutingGUI::CMD_DASHBOARD_PAGE);

        // Build and add menu item
        $dashboard_identification = $id_provider_factory->identifier('hslu_dashboard');
        $menu_items[] = $menu_item_factory->link(
                $dashboard_identification
            )
            ->withTitle($this->plugin->txt('hslu_dashboard'))
            ->withAction($link)
            ->withVisibilityCallable(
                function() use ($access_checker, $user){
                    return $access_checker->checkIfAdminRoleIsDefinedAndUserIsAdmin($user);
                }
            );

        return $menu_items;
    }
}