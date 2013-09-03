<?php
/**
 * Start_view
 *
 * @author zhangqy
 *
 */

class Star_View_Abstract {
	
	protected $_script_name = '';
	
	protected $_base_name = '';
	
	protected $_is_controller = true;
	
	protected $_is_display = true; //是否显示view
	
	protected $_controller; 
	
	protected $_postfix = '.phtml'; //后缀
	
	protected $_theme_name = 'scripts';
	
	protected $layout = '';
	
	protected $application_path;
	
	protected $default_view = 'views';
	
	protected $encoding = 'UTF-8'; //默认编码
    
    protected $js_options = array();

    public function __construct($application_path = '', $options = array())
	{
		$this->application_path = $application_path;
		
		$this->setOption($options);
		
		$this->run();
	}
	
	public function setOption($options)
	{
		if (isset($options['encoding']) && !empty($options['encoding']))
		{
			$this->setEncoding($options['encoding']);
		}

        if (isset($options['js']) && !empty($options['js']))
        {
            $this->addJsConfig($options['js']);
        }
        
        if (isset($options['base_path']) && !empty($options['base_path']))
        {
            $this->setBasePath($options['base_path']);
        }
	}
	
	protected function run()
	{

	}
	
	public function setScriptName($script_name, $is_controller = true)
	{
		$this->_is_controller = $is_controller;
		
		$this->_script_name = $script_name;
		
		return $this;
	}
	
	public function setNoRender()
	{
		$this->_is_display = false;
		
		return $this;
	}
    
    public function setRender()
    {
        $this->_is_display = true;
        
        return $this;
    }
	
	public function setBasePath($base_path)
	{
		$this->_base_name = $base_path;
		
		return $this;
	}
	
	public function getBasePath()
	{
		return $this->_base_name;
	}
	
	public function getScriptName()
	{
		return $this->_base_name;
	}
	
	public function setController($controller)
	{
		$this->_controller = $controller;
		
		return $this;
	}
	
	private function getViewPath()
	{
        $view_segments = array($this->_base_name, $this->_theme_name);
  
		if ($this->_is_controller == true)
		{           
            array_push($view_segments, $this->_controller);
		}
        
        array_push($view_segments, $this->_script_name);
        
        $view_path = Star_Loader::getFilePath($view_segments, $this->_postfix);

		return $view_path;
	}
	
    /**
     * 设置网站编码
     * 
     * @param type $encoding
     * @return \Star_View_Abstract 
     */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
		
		return $this;
	}
	
    /**
     * 返回网站编码
     * 
     * @return type 
     */
	public function getEncoding()
	{
		return $this->encoding;
	}
	
	public function loadView()
	{
		if ($this->_is_display == false)
		{
			return;
		}
		
		$view_path = $this->getViewPath();

		if (!is_file($view_path))
		{
			throw new Star_Exception( $view_path . ' not found', 500);

			exit;
		}
		
		$view_path = realpath($view_path);
		
		include $view_path;
	}
	
	public function setTheme($theme)
	{
		$this->_theme_name = $theme;
		
		return $this;
	}
	
	public function getTheme()
	{
		return $this->_theme_name;
	}
	
	public function layout()
	{
		return $this->layout;
	}
	
	public function setLayout(Star_Layout $star_layout)
	{
		$this->layout = $star_layout;
		
		return $this;
	}
	
	public function assign($key, $value = null)
	{
		if (is_array($key))
        {
            foreach ($key as $k => $val)
            {
                $this->$k = $val;
            }
        } else
        {
            $this->$key = $value;
        }
        
        return $this;
	}
    
    /**
     * 设置JS基础路径
     * 
     * @param type $path
     * @return type 
     */
    public function setJsBasePath($path)
    {
        $this->js_options['base_path'] = $path;
        
        return $this;
    }
    
    /**
     * 设置js版本号
     * 
     * @param type $version
     * @return \Star_View_Abstract 
     */
    public function setJsVersion($version)
    {
        $this->js_options['version'] = $version;
        
        return $this;
    }
    
    /**
     * 返回JS版本号
     * 
     * @return type 
     */
    public function getJsVersion()
    {
        return $this->js_options['version'];
    }
    
    /**
     * 添加js加载配置
     * 
     * @param type $options 
     */
    public function addJsConfig($options)
    {   
        //设置基础路径
        if (isset($options['base_path']) && !empty($options['base_path']))
        {
            $this->setJsBasePath($options['base_path']);
        }

        //添加加载js文件
        if (isset($options['files']) && !empty($options['files']))
        {
            $this->js_options['files'] = array_merge((array) $this->js_options['files'], (array) $options['files']);
        }
        
        //设置js版本号
        if (isset($options['version']) && !empty($options['version']))
        {
            $this->setJsVersion($options['version']);
        }
        
        return $this;
    }
    
    /**
     * 加载js文件
     * 
     * @return type 
     */
    public function loadJs()
    {
        $js_html = '';
        
        $base_path = $this->getJsBasePath();
        
        $version = $this->getJsVersion();

        if (isset($this->js_options['files']) && !empty($this->js_options['files']))
        {
            foreach ($this->js_options['files'] as $file_name)
            {
                $file_path = Star_Loader::getFilePath(array($base_path, $file_name), '.js');
                
                $js_html .= "<script type='text/javascript' src='{$file_path}?v={$version}'></script>";
            }
        }

        return $js_html;
    }
    
    /**
     * 返回js基础路径
     * 
     * @return type 
     */
    public function getJsBasePath()
    {
        return $this->js_options['base_path'];
    }
    
}

?>