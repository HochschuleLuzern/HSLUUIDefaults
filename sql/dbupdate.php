<#1>
<?php
$db = $ilDB;
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/HSLUUIDefaults/classes/class.ilHSLUUIDefaultsConfig.php';
$config_obj = new ilHSLUUIDefaultsConfig($db);
$configuration = $config_obj->getConfigurationStructure();
if (!$db->tableExists($configuration['table_name'])) {
    $fields = array(
        'config_key' => array(
            'type' => 'text',
            'length' => 64,
        ),
        'config_value' => array(
            'type' => 'text',
            'length' => 4000,
        )
    );

    $db->createTable($configuration['table_name'], $fields);
    $db->addPrimaryKey($configuration['table_name'], array('config_key'));
    foreach ($configuration['config_values'] as $key => $value) {
        $db->insert($configuration['table_name'], array('config_key' => array('text', $key), 'config_value' => array('text', '')));
    }
}
?>