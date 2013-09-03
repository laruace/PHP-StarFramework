<?php
/**
 *
 * 配置文件类
 * 
 * 处理PHP配置文件
 *
 * @author zhangqy
 *
 */


class Star_Config_Php extends Star_Config_Abstract{

	
	public function __construct($file_name, $environment = '')
	{
		parent::__construct($file_name, $environment);
	}
		
	/**
	 * 解析配置文件
	 *
	 * @param  $file_name
	 * @throws Star_Exception
	 */
	public function parseConfig()
	{
		if (!is_file($this->file_name))
		{
			throw new Star_Exception('not found: ' . $this->file_name . ' file');
		}
		
		$options = include($this->file_name);
	
		if (!array_key_exists($this->environment, $options))
		{
			throw new Star_Exception('Not fount environment set');
		}

		$config = $options[$this->environment];

		return $config;
	}
	
}

?>