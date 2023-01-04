<?php

use ILIAS\FileUpload\Location;

/**
 * Class ilHSLUUIDefaultsFilteredQueryGUI
 * @author Mark Salter <mark.salter@hslu.ch>
 */
class ilHSLUUIDefaultsFilteredQueryGUI extends ilTable2GUI
{
    /**
     * @var operators
     */
    public const OPR_EQUALS = '=';
    public const OPR_NOT_EQUALS = '!=';
    public const OPR_LIKE = 'LIKE';
    public const OPR_NOT_LIKE = 'NOT LIKE';
    public const OPR_GREATER_THAN = '>';
    public const OPR_LESS_THAN = '<';
    protected $parent;
    protected $plugin;
    protected $form_action;
    protected $filter_fields;
    protected $columns;

    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $renderer;

    /**
     * @var Standard
     */
    protected $filter;

    /**
     * @var ilUIFilterService
     */
    protected $filter_service;
    /**
     * @var sql
     */
    protected $sql;

    public function __construct(
        ilHSLUUIDefaultsCtrlRoutingGUI $parent,
        string $parent_cmd,
        $plugin,
        string $title,
        string $id,
        string $template,
        $form_action,
        array $filter_fields,
        array $columns,
        array $sql,
        string $default_order_field,
        bool $enable_header = true,
        int $limit = 1000
    ) {
        global $DIC;

        parent::__construct($parent, $parent_cmd);

        $this->parent = $parent;
        $this->plugin = $plugin;
        $this->setTitle($title);
        $this->setId($id);
        $this->setRowTemplate($template, $this->plugin->getDirectory());
        $this->form_action = $form_action;
        $this->setFormAction($form_action);
        $this->filter_fields = array_column($filter_fields, null, 'id');
        $this->columns = $columns;
        $this->setDefaultOrderField($default_order_field);
        $this->setEnableHeader($enable_header);
        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->renderer = $DIC->ui()->renderer();
        $this->filter_service = $DIC->uiService()->filter();
        $this->sql = $sql;

        foreach ($columns as $column) {
            $this->addColumn(
                $column["text"],
                $column["sort_field"],
                $column["width"],
                $column["is_checkbox_action_column"],
                $column["class"],
                $column["tooltip"],
                $column["tooltip_html"]);
        }
        $this->setLimit($limit);
    }

    protected function getFilterInputFields($filter_fields) : array
    {

        global $DIC;

        $input_fields = [];
        $field_factory = $DIC->ui()->factory()->input()->field();

        foreach ($filter_fields as $filter_field) {
            $field = null;
            switch ($filter_field["type"]) {
                case "text":
                    $field = $field_factory->text($filter_field["label"], $filter_field["byline"]);
                    break;
                case "numeric":
                    $field = $field_factory->numeric($filter_field["label"], $filter_field["byline"]);
                    break;
                case "multiSelect":
                    $field = $field_factory->multiSelect($filter_field["label"], $filter_field["options"],
                        $filter_field["byline"]);
                    break;
                default:
                    $field = $field_factory->text($filter_field["label"], $filter_field["byline"]);
            }
            $input_fields[$filter_field["id"]] = $field;
        }

        return $input_fields;
    }

    protected function setFilter(ilHSLUUIDefaultsCtrlRoutingGUI $parent, string $parent_cmd, string $id, $filter_fields)
    {

        global $DIC;
        $filter_service = $DIC->uiService()->filter();

        $this->filter = $filter_service->standard(
            $id,
            $DIC->ctrl()->getLinkTarget($parent, $parent_cmd),
            $filter_fields,
            array_fill(0, count($filter_fields), true),
            true,
            true);
    }

    private function getFilterData() : array
    {
        try {
            return $this->filter_service->getData($this->filter) ?? [];
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

    protected function fetchData() : array
    {
        global $DIC;

        $ilDB = $DIC->database();
        $where_sql = $this->sql["where"];

        // apply only set filters
        $active_filters = $this->getActiveFilters();

        $params = array();

        // build where sql
        foreach ($active_filters as $id => $filter) {
            $filter_field = $this->filter_fields[$id];
            if (strlen($filter_field["sql_expr"]) != 0) {
                $sql_expr = $filter_field["sql_expr"];
                $operator = (strlen($filter_field["operator"]) > 0) ? $filter_field["operator"] : "=";
                $value = $filter;
                $where_sql = $where_sql . ((strlen($where_sql) > 0) ? " AND " . $sql_expr : $sql_expr);
                $where_sql = $where_sql . " " . $operator;
                switch ($operator) {
                    case $this::OPR_LIKE:
                    case $this::OPR_NOT_LIKE:
                        $value = "%" . $value . "%";
                        break;
                    default:
                }
                $where_sql = $where_sql . " ? ";
                $params[] = $value;
            }
        }

        $sql_str = "SELECT " . $this->sql["select"] . " FROM " . $this->sql["from"];
        if (strlen($where_sql) > 0) {
            $sql_str .= " WHERE " . $where_sql;
        }
        if (strlen($this->sql["order_by"]) > 0) {
            $sql_str .= " ORDER BY " . $this->sql["order_by"];
        }

        // run prepared query if any params
        if (count($params) > 0) {
            $stmt = $ilDB->prepare($sql_str);
            $r = $stmt->execute($params);
        } else {
            $r = $ilDB->query($sql_str);
        }

        $arr = array();
        if ($ilDB->numRows($r) > 0) {
            while ($row = $ilDB->fetchAssoc($r)) {
                $arr[] = $row;
            }
        }

        return $arr;
    }

    protected function getActiveFilters()
    {
        // apply only set filters
        $active_filters = array_filter($this->getFilterData(), static function ($value) : bool {
            return !empty($value);
        });
        return $active_filters;
    }

    private function applyFilter(array $arr, array $filter_fields) : array
    {

        // apply only set filters
        $active_filters = $this->getActiveFilters();
        $filter_fields = $this->filter_fields;
        $self = $this;

        // apply filters
        $arr = array_filter($arr,
            static function (array $arr_data) use ($active_filters, $filter_fields, $self) : bool {

                $matches_filter = true;

                foreach ($active_filters as $id => $filter) {
                    $filter_field = $filter_fields[$id];
                    $sql_expr = $filter_field["sql_expr"];
                    if (strlen($filter_field["sql_expr"]) == 0) {
                        $operator = (strlen($filter_field["operator"]) > 0) ? $filter_field["operator"] : "=";
                        $value = $filter;
                        switch ($operator) {
                            case $self::OPR_EQUALS:
                                $matches_filter = $matches_filter && ($arr_data[$id] == $value);
                                break;
                            case $self::OPR_NOT_EQUALS:
                                $matches_filter = $matches_filter && ($arr_data[$id] != $value);
                                break;
                            case $self::OPR_LESS_THAN:
                                $matches_filter = $matches_filter && ($arr_data[$id] < $value);
                                break;
                            case $self::OPR_GREATER_THAN:
                                $matches_filter = $matches_filter && ($arr_data[$id] > $value);
                                break;
                            case $self::OPR_LIKE:
                                $matches_filter = $matches_filter && (strpos($arr_data[$id], $value) !== false);
                                break;
                            case $self::OPR_NOT_LIKE:
                                $matches_filter = $matches_filter && (strpos($arr_data[$id], $value) === false);
                                break;
                            default:
                        }
                    }

                    if (!$matches_filter) {
                        break;
                    }
                }
                return $matches_filter;
            });

        return $arr;
    }

    private function getFilterHTML() : string
    {
        return $this->renderer->render($this->filter);
    }

    public function getHTML()
    {
        $this->setFilter($this->parent, $this->parent_cmd, $this->getId(),
            $this->getFilterInputFields($this->filter_fields));

        // get data and apply filters
        $arr = $this->fetchData();
        $arr = $this->applyFilter($arr, $this->filter_fields);
        $this->setData($arr);

        // return filter and table html
        return $this->getFilterHTML() . parent::getHTML();
    }

    protected function fillRow($a_set)
    {
        foreach ($this->columns as $column) {
            $this->tpl->setVariable("VAL_COLUMN", $a_set[$column["sort_field"]]);
            $this->tpl->setCurrentBlock("column");
            $this->tpl->parseCurrentBlock();
        }
    }
}