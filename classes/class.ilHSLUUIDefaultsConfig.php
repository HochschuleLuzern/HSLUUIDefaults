<?php
declare(strict_types=1);

class ilHSLUUIDefaultsConfig
{
    private const CONFIG_TABLE_NAME = 'ui_uihk_hsluuidef_conf';
    private const CONFIG_VALUES = [
        'categories_with_fav_link' => [
            'type' => 'int[]'
        ]
    ];
    private $config = [];
    private \ilDBInterface $db;
    
    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
        if ($db->tableExists(self::CONFIG_TABLE_NAME)) {
            $this->readConfig();
        }
    }
    
    public function saveConfig($config)
    {
        foreach ($config as $key => $value) {
            if (in_array($key, array_keys(self::CONFIG_VALUES))) {
                if ($this->db->update(
                    self::CONFIG_TABLE_NAME,
                    array(
                            'config_value' => array('text', $this->returnConfigValueAsString($value))
                        ),
                    array(
                            'config_key' => array('text', $key)
                        )
                ) > 0) {
                    $r += 1;
                }
            } else {
                return -1;
            }
        }
        $this->readConfig();
        return $r;
    }
    
    public function getCategoriesWithFavLink() : array
    {
        if (!array_key_exists('categories_with_fav_link', $this->config)) {
            $this->config['categories_with_fav_link'] = [];
        }
        
        return $this->config['categories_with_fav_link'];
    }
    
    public function getCategoriesWithFavLinkAsString() : string
    {
        return $this->returnConfigValueAsString($this->getCategoriesWithFavLink());
    }
    
    public function getConfigurationStructure() : array
    {
        return [
            'table_name' => self::CONFIG_TABLE_NAME,
            'config_values' => self::CONFIG_VALUES
        ];
    }
    
    private function readConfig()
    {
        $q = $this->db->query('SELECT * FROM ' . self::CONFIG_TABLE_NAME);
        
        while ($row = $this->db->fetchAssoc($q)) {
            if (substr(self::CONFIG_VALUES[$row['config_key']]['type'], -2) == '[]') {
                $type = substr(self::CONFIG_VALUES[$row['config_key']]['type'], 0, -2);
                $row['config_value'] = $this->generateSettingsArray($row['config_value'], $type);
            } else {
                settype($row['value'], self::CONFIG_VALUES[$row['config_key']]);
            }
            
            $this->config[$row['config_key']] = $row['config_value'];
        }
    }
    
    /*
     * return $type[]
     */
    private function generateSettingsArray(string $values, string $type) : array
    {
        $values_as_array = [];
        foreach (explode(',', $values) as $value) {
            $value = trim($value);
            settype($value, $type);
            $values_as_array[] = $value;
        }
        return $values_as_array;
    }
    
    private function returnConfigValueAsString($config_value) : string
    {
        if ($config_value == null) {
            return '';
        }
        
        if (is_array($config_value)) {
            return implode(
                ',',
                array_map('trim', $config_value)
            );
        }
            
        return $config_value;
    }
}
