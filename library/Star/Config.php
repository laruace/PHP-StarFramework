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
	
    protected static $instance = null;
    
    protected static $configs = array();

    public function __construct($file_name, $environment = '')
	{
		$this->file_name = $file_name;
		
		$this->environment = $environment;
		
		$this->init($file_name, $environment);
	}
	
	protected function init($file_name, $environment)
	{
		$class_name = "Star_Config_" . ucfirst(array_pop(explode('.', $file_name)));
        
        self::$instance = new $class_name($file_name, $environment);
	}
	  
	/**
	 * 返回配置项
	 */
	public function loadConfig()
	{
		$options = self::$instance->parseConfig();
        
        Star_Registry::set('config', $options);
        
		return (array) $options;
	}
	
    public function get($key = null)
    {
        return isset(self::$configs[$key]) ? self::$configs[$key] : '';
    }
}

?>