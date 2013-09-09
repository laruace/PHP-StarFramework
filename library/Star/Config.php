<?php
/**
 *
 * 配置文件类
 *
 * @author zhangqy
 *
 */


class Star_Config {
	
	protected $file_name;
	
	protected $environment;
	
    protected $config = null;
    
    protected static $options = array();

    public function __construct($file_name, $environment = '')
	{
		$this->file_name = $file_name;
		
		$this->environment = $environment;
		
		$this->init($file_name, $environment);
	}
	
	protected function init($file_name, $environment)
	{
        $config_type = ucfirst(array_pop(explode('.', $file_name)));
        
		$class_name = "Star_Config_" . $config_type;
        
        $class_path = "Star/Config/{$config_type}.php";
        
        require $class_path;
        
        $this->config = new $class_name($file_name, $environment);
	}
	  
	/**
	 * 返回配置项
	 */
	public function loadConfig()
	{
        
		$options = $this->config->parseConfig();
        
        self::$options = $options;
        
        Star_Registry::set('config', $options);
        
		return (array) $options;
	}
	
    public static function get($key = null)
    {
        return isset(self::$options[$key]) ? self::$options[$key] : '';
    }
}

?>