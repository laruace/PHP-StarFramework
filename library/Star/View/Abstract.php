<?php
/**
 * Start_view
 *
 * @author zhangqy
 *
 */

abstract class Star_View_Abstract {
	
	protected $_script_name = '';
	
	protected $_base_name = '';
	
	protected $_is_controller = true;
	
	protected $_is_display = true; //是否显示view
	
	protected $_controller; 
    
    protected $_action;

    protected $_postfix = '.phtml'; //后缀
	
	protected $_theme_name = 'scripts';
	
	protected $layout = '';
	
	protected $application_path;
	
	protected $default_view = 'views';
	
	protected $encoding = 'UTF-8'; //默认编码
    
    protected $js_options = array();
    
    protected $css_options = array();

    protected $is_cache = false; //页面是否缓存
    
    protected $timeout = 600; //默认缓存时间
    
    protected $cache_directory = 'caches'; //缓存目录
    
    protected $cache_name = 'index'; //缓存名
    
    protected $is_flush = false; //是否强制刷新缓存


    protected $data = array();

    public function __construct($application_path = '', $options = array())
	{
		$this->application_path = $application_path;
		
		$this->setOption($options);
		
		$this->run();
	}
	
	abstract protected function setOption(Array $options);
	
	protected function run()
	{

	}
	
	public function setScriptName($script_name, $is_controller = true)
	{
		$this->_is_controller = $is_controller;
		
		$this->_script_name = $script_name;
		
		return $this;
	}
	
    protected function setDisplay($is_display)
    {
        if ($is_display == true)
        {
            $this->setRender();
        } else
        {
            $this->setNoRender();
        }
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
    
    public function setAction($action)
    {
        $this->_action = $action;
        
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
                $this->data[$k] = $val;
            }
        } else
        {
            $this->data[$key] = $value;
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
     * 设置css基础路径
     * 
     * @param type $path
     * @return \Star_View_Abstract 
     */
    public function setCssBasePath($path)
    {
        $this->css_options['base_path'] = $path;
        
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
     * 设置css样式版本号
     * 
     * @param type $version
     * @return \Star_View_Abstract 
     */
    public function setCssVersion($version)
    {
        $this->css_options['version'] = $version;
        return $this;
    }
    
    /**
     * 设置css加载文件
     * 
     * @param type $files 
     */
    public function setJsFiles($files)
    {
        $this->js_options['files'] = array_merge((array) $this->js_options['files'], (array) $files);

        return $this;
    }
    
    /**
     * 设置css加载文件
     * 
     * @param type $files
     * @return \Star_View_Abstract 
     */
    public function setCssFiles($files)
    {
        $this->css_options['files'] = array_merge((array) $this->css_options['files'], (array) $files);
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
     * 返回css版本号
     * 
     * @return type 
     */
    public function getCssVersion()
    {
        return $this->css_options['version'];
    }
    
    /**
     * 设置css配置文件
     * 
     * @param type $options
     * @return \Star_View_Abstract 
     */
    public function setCssConfig($options)
    {
        //设置基础路径
        if (isset($options['base_path']) && !empty($options['base_path']))
        {
            $this->setCssBasePath($options['base_path']);
        }

        //添加加载css文件
        if (isset($options['files']) && !empty($options['files']))
        {
            $this->setCssFiles($options['files']);
        }
        
        //设置css版本号
        if (isset($options['version']) && !empty($options['version']))
        {
            $this->setCssVersion($options['version']);
        }
        
        return $this;
    }


    /**
     * 添加js加载配置
     * 
     * @param type $options 
     */
    public function setJsConfig($options)
    {   
        //设置基础路径
        if (isset($options['base_path']) && !empty($options['base_path']))
        {
            $this->setJsBasePath($options['base_path']);
        }

        //添加加载js文件
        if (isset($options['files']) && !empty($options['files']))
        {
            $this->setJsFiles($options['files']);
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
            foreach ((array) $this->js_options['files'] as $file_name)
            {
                $file_path = Star_Loader::getFilePath(array($base_path, $file_name), '.js', '/');
                
                $js_html .= "<script type='text/javascript' src='{$file_path}?v={$version}'></script>";
            }
        }

        return $js_html;
    }
    
    public function loadCss()
    {
        $css_html = '';
        $base_path = $this->getCssBasePath();
        $version = $this->getCssVersion();
        
        if (isset($this->css_options['files']) && !empty($this->css_options['files'])) 
        {
            foreach ((array) $this->css_options['files'] as $file_name)
            {
                $file_path = Star_Loader::getFilePath(array($base_path, $file_name), '.css', '/');
                $css_html .= "<link rel='stylesheet' type='text/css' href='{$file_path}?v={$version}' />";
            }
        }
        
        return $css_html;
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
    
    public function getCssBasePath()
    {
        return $this->css_options['base_path'];
    }
    
    //设置缓存超时时间
    public function setCacheTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
        
        return $this;
    }
    
    /**
     * 设置缓存路径
     * 
     * @param type $directory
     * @return \Star_View_Abstract 
     */
    public function setCacheDirectory($directory)
    {
        !empty($directory) && $this->cache_directory = $directory;
        return $this;
    }
    
    /**
     * 是否缓存
     * 
     * @return type 
     */
    public function isCache()
    {
        return $this->is_cache;
    }
    
    //缓存是否过期
    public function cacheIsExpire()
    {
        $cache_path = $this->getCacheFileName();
        
        if (!file_exists($cache_path) || (time() - filemtime($cache_path) >= $this->timeout) || $this->is_flush == true)
        {
            return true;
        } else
        {
            return false;
        }
    }

    /**
     * 读取缓存信息
     * 
     * @param type $cache_path
     * @return boolean
     * @throws Star_Exception 
     */
    public function loadCache()
    {
        $cache_path = $this->getCacheFileName(); //缓存文件
        
        //判断文件是否存在
        if (!is_file($cache_path))
        {
            return false;
        }
        
        //文件是否可以
        if (!is_readable($cache_path))
        {
            throw new Star_Exception("Connt open $cache_path for read");
        }

        //缓存是否超时
        if ($this->cacheIsExpire() == true)
        {
            return false;
        }
        
        include $cache_path; //载入缓存文件
    }
    
    public function flushCache()
    {
        return $this->is_flush = true;
    }


    //保存缓存内容
    public function saveCache($body)
    {
        $file_name = $this->getCacheFileName();
        
        if (!is_dir(dirname($file_name)))
        {
            mkdir(dirname($file_name), 0777, true);
        }
        
        if (false === ($handle = @fopen($file_name, 'w')))
        {
            throw new Star_Exception("Connt open $file_name for writing");
        }

        fwrite($handle, $body);
        fclose($handle);
    }

    //开启页面缓存
    public function openCache($cache_name = 'index', $timeout = 0, $is_flush = false)
    {
        if ($timeout > 0)
        {
            $this->setCacheTimeout($timeout);
        }
        
        if (!empty($cache_name))
        {
            $this->setCacheName($cache_name);
        }
        
        if ($is_flush == true)
        {
            $this->flushCache();
        }
        
        $this->is_cache = true;
    }
    
    public function setCacheName($cache_name)
    {
        $this->cache_name = $cache_name;
    }
    
    //返回文件路径
    public function getCacheFileName()
    {
        $segments = array(
            $this->application_path,
            $this->cache_directory,
            $this->_controller,
            $this->_action,
            $this->cache_name
        );
        
        $path = Star_Loader::getFilePath($segments, '.html');
  
        return $path;
    }
    
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __get($name) {
        return $this->data[$name];
    }
}

?>