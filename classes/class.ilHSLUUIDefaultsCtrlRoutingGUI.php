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

    // TAB-IDs
    private const TAB_DASHBOARD = 'dashboard';

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
    private $filtered_queries = array();

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
        $this->filtered_queries = array();

        $this->access_checker = new ilHSLUUIDefaultsAccessChecker($DIC->rbac()->review());

        //  add the filtered queries
        $this->addFilteredQueries();

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
            default:
                // execute the relevant filtered query
                $this->tabs->activateTab($this->filtered_queries[$cmd]->getId());
                $this->showFilteredQueryPage($this->filtered_queries[$cmd]);
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

        // add tabs for filtered queries
        $this->addFilteredQueryTabs();

    }
    private function addFilteredQueries()
    {
        //
        // event log
        //
        $columns = [
            ["text" => $this->plugin_object->txt("evento_id"),"sort_field" => "evento_id", "width" => "7%"],
            ["text" => $this->plugin_object->txt("last_import_data"), "sort_field" => "last_import_data", "width" => "83%"],
            ["text" => $this->plugin_object->txt("last_import_date"), "sort_field" => "last_import_date", "width" => "10%"]
        ];

        $filter_fields = [
            ["type" => "text", "id" => "evento_id", "label" => $this->plugin_object->txt("evento_id"), "sql_expr" => "evento_id", "operator" => ilHSLUUIDefaultsFilteredQueryGUI::OPR_LIKE],
            ["type" => "text", "id" => "last_import_data", "label" => $this->plugin_object->txt("last_import_data"), "sql_expr" => "last_import_data", "operator" => ilHSLUUIDefaultsFilteredQueryGUI::OPR_LIKE],
            ["type" => "text", "id" => "last_import_date", "label" => $this->plugin_object->txt("last_import_date"), "sql_expr" => "last_import_date", "operator" => ilHSLUUIDefaultsFilteredQueryGUI::OPR_LIKE]
        ];

        $sql = [
            "select" => "*",
            "from" => "crevento_log_events",
            "where" => "",
            "order_by" => "last_import_date"
        ];

        $filtered_query = new ilHSLUUIDefaultsFilteredQueryGUI($this, "show_evento_log", $this->plugin_object, $this->plugin_object->txt("evento_log"), "hsluevento", "tpl.table_row_single_column.html", $this->ctrl->getFormAction($this), $filter_fields, $columns, $sql, "last_import_date");

        $this->filtered_queries[$filtered_query->getParentCmd()] = $filtered_query;

        //
        // objects
        //
        $columns = [
            ["text" => $this->plugin_object->txt("obj_id"),"sort_field" => "obj_id", "width" => "7%"],
            ["text" => $this->plugin_object->txt("title"), "sort_field" => "title", "width" => "40%"],
            ["text" => $this->plugin_object->txt("type"), "sort_field" => "type", "width" => "6%"],
            ["text" => $this->plugin_object->txt("ref_id"), "sort_field" => "ref_id", "width" => "7%"],
            ["text" => $this->plugin_object->txt("path"), "sort_field" => "path", "width" => "40%"]
        ];

        $filter_fields = [
            ["type" => "text", "id" => "obj_id", "label" => $this->plugin_object->txt("obj_id"), "sql_expr" => "ref.obj_id", "operator" => ilHSLUUIDefaultsFilteredQueryGUI::OPR_LIKE],
            ["type" => "text", "id" => "ref_id", "label" => $this->plugin_object->txt("ref_id"), "sql_expr" => "ref.ref_id", "operator" => ilHSLUUIDefaultsFilteredQueryGUI::OPR_LIKE],
            ["type" => "text", "id" => "title", "label" => $this->plugin_object->txt("title"), "sql_expr" => "obj.title", "operator" => ilHSLUUIDefaultsFilteredQueryGUI::OPR_LIKE]
        ];

        $sql = [
            "select" => "obj.obj_id AS obj_id, CASE WHEN obj.type in ('cat','crs','grp','crsr','file') THEN concat('<a href=ilias.php?ref_id=', ref.ref_id, '&cmdClass=ilrepositorygui&cmdNode=wq&baseClass=ilrepositorygui>',obj.title,'</a>') ELSE obj.title END as title, obj.type AS type, ref.ref_id AS ref_id, t.path AS path ",
            "from" => "object_reference AS ref JOIN object_data obj ON ref.obj_id = obj.obj_id JOIN tree as t ON ref.ref_id = t.child",
            "where" => "",
            "order_by" => "obj.obj_id"
        ];

        $filtered_query = new ilHSLUUIDefaultsFilteredQueryGUI($this, "show_objects", $this->plugin_object, $this->plugin_object->txt("objects"), "hsluobjects", "tpl.table_row_single_column.html", $this->ctrl->getFormAction($this), $filter_fields, $columns, $sql, "obj_id");

        $this->filtered_queries[$filtered_query->getParentCmd()] = $filtered_query;
    }
    private function addFilteredQueryTabs()
    {
        foreach($this->filtered_queries as $key=> $query){
            $link = $this->ctrl->getLinkTargetByClass(self::CTRL_UI_ROUTE, $query->getParentCmd());
            $this->tabs->addTab($query->getId(), $query->title, $link);
        }
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

     private function showFilteredQueryPage(ilHSLUUIDefaultsFilteredQueryGUI $filtered_query)
     {
         $this->tpl->setContent($filtered_query->getHTML());
    }
}
