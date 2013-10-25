<?php

require 'Star/Loader/Loader.php';

require 'Star/Layout/Layout.php';

require 'Star/Config.php';

require 'Star/Registry.php';

require 'Star/Model/Abstract.php';

class Star_Application {

    protected $display_exceptions = false;

    private $request;
	
	protected $application_path;
    
    protected $controller_directory;

    protected $view;
	
	protected $bootstrap = null;
	
	public function __construct($application_env, $application_path, $config_file, $star_path = '')
	{
		$this->application_path = $application_path;
		
		$this->setAutoload($star_path);
        
		$star_config = new Star_Config($config_file, $application_env);
		$options = $star_config->loadConfig();
		$this->setOption($options);
        $this->iniRequest();
	}

    /**
     * run application 
     */
	public function run()
	{
		$this->dispatch();
	}
	
	/**
	 *  设置参数
	 *
	 * @param array $options
	 */
	protected function setOption(array $options)
	{
        /*
        if (isset($options['phpSetting']) && !empty($options['phpSetting']))
        {
            $this->setPhpSettings($options['phpSetting']);
        }
        
        if (isset($options['bootstrap']) && !empty($options['bootstrap']))
        {
            $this->setBootstrap($options['bootstrap']);
        }
        
        if (isset($options['resources']) && !empty($options['resources']))
        {
            $this->setResources($options['resources']);
        }
        
        if (isset($options['cache']) && !empty($options['cache']))
        {
            $this->setCache($options['cache']);
        }
        
        return ;
         * 
         */
        
        if (is_array($options))
        {
            $methods = get_class_methods($this);
            
            foreach ($options as $key => $option)
            {
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods))
                {
                    $this->$method($option);
                }
            }
        }
	}
	
    protected function setFrontController($options)
    {
        if (isset($options['controllerDirectory']) && !empty($options['controllerDirectory']))
        {
            $this->setControllerDirectory($options['controllerDirectory']);
        }

        if (isset($options['params']['display_exceptions']))
        {
            $this->display_exceptions = $options['params']['display_exceptions'];
        }
        
        return $this;
    }
    
    public function setControllerDirectory($path)
    {
        $this->controller_directory = $path;
        
        return $this;
    }
    
    public function getControllerDirectory()
    {
        if ($this->controller_directory == null)
        {
            $directory_name = Star_Loader::getLoadTypeByKey($this->request->getControllerkey());
            $this->controller_directory = Star_Loader::getModuleDirect($directory_name);
        }
        
        return $this->controller_directory;
    }

    /**
     * 设置view
     * 
     * @param type $application_path
     * @param type $options
     * @return \Star_Application 
     */
	protected function setView($options)
	{
        require 'Star/View.php';
		$this->view = new Star_View($this->application_path, $options);
		
		return $this;
	}
	
    /**
     * 设置Bootstarp
     * 
     * @param type $bootstrap_path
     * @param string $class
     * @throws Star_Exception 
     */
	public function setBootstrap($options)
	{
        $bootstrap_path = isset($options['path']) ? $options['path'] : '';
        $class = isset($options['class']) && !empty($options['class']) ? $options['class'] : 'Bootstrap';
        
        if (empty($bootstrap_path))
        {
            return ;
        }

        if (!file_exists($bootstrap_path))
        {
            throw new Star_Exception('Not found Bootstrap file:' . $bootstrap_path);
        }
        
        require $bootstrap_path;
        
        if (!class_exists($class, false))
        {
            throw new Star_Exception('bootstrap object ' . $class . ' not found in:' . $bootstrap_path);
        }
		
		$this->bootstrap = new $class($this);
	}
	
	/**
	 * 设置PHP配置项
	 *
	 * @param array $options
	 */
	public function setPhpSettings(array $options)
	{
		foreach ($options as $key => $value)
		{
			if (is_scalar($value))
			{
				ini_set($key, $value);
			} else if (is_array($value))
			{
				$this->setPhpSetting($value);
			}
		}
	}
	
    /**
     * 设置资源
     * 
     * @param type $options 
     */
	public function setResources($options)
	{
        if (isset($options['frontController']) && !empty($options['frontController']))
        {
            $this->setFrontController($options['frontController']);
        }
        
        $this->setView(isset($options['view']) ? $options['view'] : array());
        
        //DB配置
		if (isset($options[Star_Model_Abstract::ADAPTER]) && $options[Star_Model_Abstract::ADAPTER])
		{
            call_user_func(array('Star_Model_Abstract', 'setting'), $options);
		}
	}
	
    /**
     * 设置缓存
     * 
     * @param array $options 
     */
	public function setCache(array $options)
	{
		if ($options['is_cache'] == true)
		{
            require 'Star/Cache.php';
			Star_Cache::initInstance($options);
		}
	}
	
	public function bootstrap($resource = null)
	{
        if ($this->bootstrap !=null)
        {
            $this->getBootstrap()->bootstrap($resource);
        }
		return $this;
	}
	
	public function getBootstrap()
	{
		return $this->bootstrap;
	}
	
    /**
     * 分发
     * 
     * @return type 
     */
	private function dispatch()
	{
		$request = $this->request;
        header('Cache-Control: private');
		header('Content-Type: text/html; charset=' . $this->view->getEncoding());
		ob_start();

        try{
            require 'Star/Controller/Action.php';
            $controller_class = $this->loadController($request); //返回controller
            $controller = new $controller_class($request, $this->view); //实例化controller
            $action = $request->getAction(); //返回action
            $controller->dispatch($action); //执行action
            call_user_func(array('Star_Model_Abstract', 'Close')); //主动关闭数据库链接
        } catch (Exception $e)
        {
            return $this->handleException($e);
        }
        
        //开启layout 加载layout
		if ($controller->layout instanceof Star_Layout && $controller->layout->isEnabled() == true)
		{
			$body = ob_get_contents();
			ob_clean();
			$this->setLayout($controller->view, $controller->layout, $body);
		}
        
        $this->saveViewCache($controller->view); //存储页面缓存
        
		ob_end_flush();
	}
    
    /**
     * 加载controller
     * 
     * @param type $request
     * @return type
     * @throws Star_Exception 
     */
    public function loadController($request)
    {
        $class_name = $request->getController();
        $file_path = Star_Loader::getFilePath(array($this->getControllerDirectory(), $class_name));

        if (Star_Loader::isExist($file_path) == false)
        {
            throw new Star_Exception("{$file_path} not found!", 404);
        }
        
        //文件是否可读
        if (!Star_Loader::isReadable($file_path))
        {
            throw new Star_Exception("Connot load controller calss {$class_name} from file {$file_path}", 500);
        }
        
        require $file_path;
        
        //类是否存在
        if (!class_exists($class_name, false))
        {
            throw new Star_Exception("Invalid controller class ({$class_name}) from file {$file_path}", 404);
        }

        return $class_name;
    }
	
    /**
     * 设置layout
     * 
     * @param type $star_view
     * @param type $star_layout
     * @param type $body 
     */
	public function setLayout($star_view, $star_layout, $body)
	{
		$star_layout->setView($star_view);
		$star_layout->assign($star_layout->getContentKey(), $body);
		$star_layout->render();
	}
	
	/**
	 * 设置导入文件目录
	 * 	 * @param array $options
	 */
	public function setIncludePaths(array $options)
	{
		foreach ($options as $value)
		{
			if (is_string($value) && is_dir($value))
			{
				set_include_path($value);
			} else if (is_array($value))
			{
				$this->setIncludePaths($value);
			}
		}
	}
	
	/**
	 * 设置自动加载
	 */
	public function setAutoload($star_path)
	{
		$star_autoload = new Star_Loader();
		$star_autoload->setApplicationPath($this->application_path)->setStarPath($star_path);
		spl_autoload_register(array('Star_Loader', 'autoload'));
		return $this;
	}
	
	/**
     * 初始化request 
     */
	protected function iniRequest()
	{
        require 'Star/Http/Request.php';
		$request = new Star_Http_Request();
		$this->request = $request;
	}
    
    /**
     * 设置默认controller_name
     * 
     * @param type $controller_name
     * @return \Star_Application 
     */
    public function setDefaultControllerName($controller_name)
    {
        $this->request->setDefaultControllerName($controller_name);
        return $this;
    }
    
    /**
     * 设置默认action_name
     * 
     * @param type $action_name
     * @return \Star_Application 
     */
    public function setDefaultActionName($action_name)
    {
        $this->request->setDefaultActionName($action_name);
        return $this;
    }
    
    public function setControllerKey($controller_key)
    {
        $this->request->setControllerKey($controller_key);
        return $this;
    }
    
    public function setActionKey($action_key)
    {
        $this->request->setActionKey($action_key);
        return $this;
    }
    
    /**
     * 保存页面缓存 
     */
    protected function saveViewCache(Star_View $view)
    {
        //开启缓存，且缓存超时或者缓存不存在，则写入缓存
        if ($view->isCache() == true && $view->cacheIsExpire() == true)
        {
            $view->saveCache(ob_get_contents());
        }
        
        return $view->isCache();
    }
    
    public function handleException($e)
    {
        if ($e->getCode() == 404)
        {
            return header('Location: /404.html');
        }
        
        if ($this->display_exceptions == true)
        {
            echo $e->__toString();
        }else{
            call_user_func(array('Star_log', 'log'), $e->__toString());
        }
        
        if ($e->getCode() == 500)
        {
            //
        }
    }
}