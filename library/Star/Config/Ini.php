<?php
/**
 *
 * 配置文件类
 * 
 * 处理.Ini配置文件
 *
 * @author zhangqy
 *
 */


class Star_Config_Ini extends Star_Config_Abstract{
	
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
		
		$ini_array = parse_ini_file($this->file_name, true);
		
		if (!array_key_exists($this->environment, $ini_array))
		{
			throw new Star_Exception('Not fount environment set');
		}

		$ini_array = $ini_array[$this->environment];
		
		$config = array();

		if (!empty($ini_array) && is_array($ini_array))
		{
			foreach ($ini_array as $key => $value)
			{
				$config = $this->processKey($config, explode('.', $key), $value);
			}
		}

		return $config;
	}
	
    /**
     * 针对数组key进行处理
     * 
     * @param type $config
     * @param type $keys
     * @param type $value
     * @return type 
     */
	protected function processKey($config, $keys, $value)
	{
		if (!empty($keys) && is_array($keys))
		{
			$key = array_shift($keys);

			if (!empty($keys) && is_array($keys))
			{
				if (isset($config[$key]))
				{
					$config[$key] = $this->processKey($config[$key], $keys, $value);
				} else
				{
					$config[$key] = $this->processKey(array(), $keys, $value);
				}
			} else
			{
				$config[$key] = $value;
			}
		}
		
		return $config;
	}
	
}

?>