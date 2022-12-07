<?php declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;

/**
 * Class ilHSLUUIDefaultsCtrlRoutingGUI
 * @author
 * @ilCtrl_isCalledBy    ilHSLUUIDefaultsCtrlRoutingGUI: ilUIPluginRouterGUI
 */
class ilHSLUUIDefaultsCtrlRoutingGUI
{
    // Use this constants as 'class' for $ilCtrl->getLinkByTargetClass(...)
    public const CTRL_UI_ROUTE = [\ilUIPluginRouterGUI::class, \ilHSLUUIDefaultsCtrlRoutingGUI::class];

    // Commands which are used for ilCtrl. Public = accessible for other GUI-Classes, Private = only accessible from here
    public const CMD_DASHBOARD_PAGE = 'show_dashboard';
    private const CMD_BLANK_PAGE = 'show_blank_page';

    // TAB-IDs
    private const TAB_DASHBOARD = 'dashboard';
    private const TAB_BLANK = 'blank';

    private ilHSLUUIDefaultsPlugin $plugin_object;
    private ilGlobalPageTemplate $tpl;
    private Factory $ui_factory;
    private Renderer $ui_renderer;
    private ilCtrl $ctrl;
    private ilTabsGUI $tabs;
    private ilDBInterface $db;
    private ServerRequestInterface $request;
    private ilTree $tree;
    private ilObjUser $user;
    private ilErrorHandling $error;
    private ilHSLUUIDefaultsAccessChecker $access_checker;

    public function __construct()
    {
        global $DIC;

        // Here I set a lot of useful objects which might be used later in the GUI class
        $this->plugin_object = new ilHSLUUIDefaultsPlugin();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->ui_factory = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC->tabs();
        $this->db = $DIC->database();
        $this->request = $DIC->http()->request();
        $this->tree = $DIC->repositoryTree();
        $this->user = $DIC->user();
        $this->error = $DIC["ilErr"];

        $this->access_checker = new ilHSLUUIDefaultsAccessChecker($DIC->rbac()->review());
    }

    public function executeCommand()
    {
        // This method is for access checking. We only want admins on this page. Otherwise redirect with an error
        $this->checkAccessAndRedirectOnFailure();

        // This method renders the title, tabs and all the stuff you want to show on each page
        $this->initHeaderGUI();

        // Here is the check for the ilCtrl-Command. I prefer to use class-constants instead of strings to avoid typos
        $cmd = $this->ctrl->getCmd();
        switch($cmd) {
            case self::CMD_DASHBOARD_PAGE:
                $this->tabs->activateTab(self::TAB_DASHBOARD);
                $this->showDashboardPage();
                break;

            case self::CMD_BLANK_PAGE:
                $this->tabs->activateTab(self::TAB_BLANK);
                $this->showBlankPage();
                break;
        }

        // Add the end, we let the Global-Template send its content to the client
        $this->tpl->printToStdout();
    }

    private function initHeaderGUI()
    {
        // Set title which is shown above the tabs
        $this->tpl->setTitle("HSLU UI Defaults");

        // Add tab for dashboard
        $link = $this->ctrl->getLinkTargetByClass(self::CTRL_UI_ROUTE, self::CMD_DASHBOARD_PAGE);
        $this->tabs->addTab(self::TAB_DASHBOARD, $this->plugin_object->txt('tab_dashboard'), $link);

        // Add tab for blank page
        $link = $this->ctrl->getLinkTargetByClass(self::CTRL_UI_ROUTE, self::CMD_BLANK_PAGE);
        $this->tabs->addTab(self::TAB_BLANK, $this->plugin_object->txt('tab_blank'), $link);
    }

    private function checkAccessAndRedirectOnFailure()
    {
        if (!$this->access_checker->checkIfAdminRoleIsDefinedAndUserIsAdmin($this->user)) {
            $this->error->raiseError('Permission denied');
            exit;
        }
    }

    private function showDashboardPage()
    {
        // Array for UI-Components. See UI-Kitchensink documentation for more
        $ui_components = [];

        // Build modal from UI-Factory and add to UI-Components
        $modal = $this->ui_factory->modal()->lightbox(
            $this->ui_factory->modal()->lightboxTextPage('This is a lightbox modal', 'And here is some text')
        );
        $ui_components[] = $modal;

        // Build panel with a button from UI-Factory. Let the modal open itself if the button is clicked
        $ui_components[] = $this->ui_factory->panel()->standard(
            'This is a panel for something',
            $this->ui_factory->button()->standard('Klick here to show a modal', $modal->getShowSignal())
        );

        // Render kitchensink-components to HTML and add them to the globale template
        $content_html = $this->ui_renderer->render($ui_components);
        $this->tpl->setContent($content_html);
    }

    private function showBlankPage()
    {
        // Just set an ampty content since this is a blank page
        $this->tpl->setContent('');
    }
}