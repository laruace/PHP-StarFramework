<?php
/**
 * @package library\Star
 */

/**
 *
 * 配置文件类
 *
 * @package library\Star
 * @author zhangqy
 *
 */
class Star_Config {
	
	protected $file_name;
	
	protected $environment;
	
    protected $config = null;
    
    protected static $options = array();

    /**
     * 构造方法
     * @param unknown $file_name
     * @param string $environment
     */
    public function __construct($file_name, $environment = '')
	{
		$this->file_name = $file_name;
		
		$this->environment = $environment;
		
		$this->init($file_name, $environment);
	}
	
	/**
	 * 初始化
	 * @param unknown $file_name
	 * @param unknown $environment
	 */
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
        if (empty(self::$options))
        {
            $options = $this->config->parseConfig();

            self::$options = (array) $options;
        }
		return self::$options;
	}
	
	/**
	 * 获取配置项
	 * @param string $key
	 * @return multitype:
	 */
    public static function get($key = null)
    {
        return isset(self::$options[$key]) ? self::$options[$key] : self::$options;
    }
}

?>