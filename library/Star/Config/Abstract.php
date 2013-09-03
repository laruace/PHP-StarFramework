<?php
/**
 *
 * 配置文件类抽象类
 * 
 * 处理.Ini配置文件
 *
 * @author zhangqy
 *
 */


Abstract class Star_Config_Abstract {
	
	protected $file_name;
	
	protected $environment;
	
	public function __construct($file_name, $environment = '')
	{
		$this->file_name = $file_name;
		
		$this->environment = $environment;
        
	}
	
	/**
	 * 返回配置项
	 */
	public function loadConfig()
	{
		$options = $this->parseConfig();

		return (array) $options;
	}
    
    public abstract function parseConfig();
	
}

?>