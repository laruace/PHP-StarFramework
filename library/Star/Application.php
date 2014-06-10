<?php
/**
 * @package library\Star
 */

/**
 * 导入文件
 */
require 'Star/Loader.php';
require 'Star/Layout.php';
require 'Star/Config.php';
require 'Star/Model/Abstract.php';

/**
 * 应用基类
 * 
 * @package library\Star
 * @author zqy
 *
 */
class Star_Application {

    protected $display_exceptions = false; //是否显示异常 开发测试环境打开方便调试

    private $request; //star_request类
	
	protected $application_path; //app路径
    
    protected $controller_directory; //应用controller路径

    protected $view; //star_view类
	
	protected $bootstrap = null; //应用boostrap
	
    /**
     * 构造方法
     * 
     * @param type $application_env 配置变量
     * @param type $application_path app路径
     * @param type $config_file 配置文件路径
     * @param type $library_path  类库路径
     */
	public function __construct($application_env, $application_path, $config_file, $library_path = '')
	{
		$this->application_path = $application_path;

		$this->setAutoload($library_path);
        
		$star_config = new Star_Config($config_file, $application_env);
		$options = $star_config->loadConfig();
        $this->iniRequest();
		$this->setOption($options);
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
        
        if (!empty($options))
        {
            $methods = get_class_methods($this);
            $methods = array_flip($methods);
            foreach ($options as $key => $option)
            {
                $method = 'set' . ucfirst($key);
                if (array_key_exists($method, $methods))
                {
                    $this->$method($option);
                }
            }
        }
        
        //配置文件没有配置view, 初始化view
        if (!isset($options['resources']['view']))
        {
            $this->setView(array());
        }
	}
	
    /**
     * 设置controller配置
     * 
     * @param type $options
     * @return \Star_Application 
     */
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
    
    /**
     * 设置controller目录
     * 
     * @param type $path
     * @return \Star_Application 
     */
    public function setControllerDirectory($path)
    {
        $this->controller_directory = $path;
        
        return $this;
    }
    
    /**
     * 返回controller目录
     * 
     * @return type 
     */
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
		
		$this->bootstrap = new $class($this->request);
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
        
        //初始化view
        if (isset($options['view']))
        {
            $this->setView(!empty($options['view']) ? $options['view'] : array());
        }
        
        //DB配置
		if (isset($options[Star_Model_Abstract::ADAPTER]) && $options[Star_Model_Abstract::ADAPTER])
		{
            call_user_func(array('Star_Model_Abstract', 'setting'), $options[Star_Model_Abstract::ADAPTER]);
		}
        
        //初始化缓存
        if (isset($options['cache']))
        {
            $this->setCache($options['cache']);
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
	
    /**
     * 执行bootstrap
     * 
     * @param type $resource
     * @return \Star_Application 
     */
	public function bootstrap($resource = null)
	{
        if ($this->bootstrap !=null)
        {
            $this->getBootstrap()->bootstrap($resource);
        }
		return $this;
	}
	
    /**
     * 返回bootstrap
     * 
     * @return type 
     */
	public function getBootstrap()
	{
		return $this->bootstrap;
	}
	
    /**
     * 消息派遣 调用控制器
     * 
     * @return type 
     */
	private function dispatch()
	{
        header('Cache-Control: no-cache');
		header('Content-Type: text/html; charset=' . $this->view->getEncoding());
		ob_start();

        try{
            require 'Star/Controller/Action.php';
            $controller = $this->loadController(); //实例化controller
            $action = $this->request->getAction(); //返回action
            $controller->dispatch($action); //执行action
            call_user_func(array('Star_Model_Abstract', 'Close')); //主动关闭数据库链接
        } catch (Exception $e)
        {
            return $this->handleException($e);
        }
        
		ob_end_flush();
	}
    
    /**
     * 加载controller
     * 
     * @param type $request
     * @return type
     * @throws Star_Exception 
     */
    public function loadController()
    {
        $contoller = $this->request->getController();
        $file_path = Star_Loader::getFilePath(array($this->getControllerDirectory(), $contoller));

        if (Star_Loader::isExist($file_path) == false)
        {
            throw new Star_Exception("{$file_path} not found!", 404);
        }
        
        //文件是否可读
        if (!Star_Loader::isReadable($file_path))
        {
            throw new Star_Exception("Connot load controller calss {$contoller} from file {$file_path}", 500);
        }
        
        require $file_path;
        
        //类是否存在
        if (!class_exists($contoller, false))
        {
            throw new Star_Exception("Invalid controller class ({$contoller}) from file {$file_path}", 404);
        }

        return new $contoller($this->request, $this->view);
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
	public function setAutoload($library_path)
	{
		$star_autoload = new Star_Loader();
		$star_autoload->setApplicationPath($this->application_path)->setLibraryPath($library_path);
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
     * 设置默认action_name 初始是Action
     * 
     * @param type $action_name
     * @return \Star_Application 
     */
    public function setDefaultActionName($action_name)
    {
        $this->request->setDefaultActionName($action_name);
        return $this;
    }
    
    /**
     * 设置controllerKey 初始是Controller
     * 
     * @param type $controller_key
     * @return \Star_Application 
     */
    public function setControllerKey($controller_key)
    {
        $this->request->setControllerKey($controller_key);
        return $this;
    }
    
    /**
     * 设置actionKey 初始是Action
     * 
     * @param type $action_key
     * @return \Star_Application 
     */
    public function setActionKey($action_key)
    {
        $this->request->setActionKey($action_key);
        return $this;
    }
    
    /**
     * 是否显示异常
     * 
     * @param type $flag
     * @return type 
     */
    protected function setDisplayException($flag = false)
    {
        return $this->display_exceptions = $flag;
    }

    /**
     * 处理异常
     * 
     * @param type $e
     * @return type 
     */
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
            call_user_func(array('Star_Log', 'log'), $e->__toString());
        }
        
        if ($e->getCode() == 500)
        {
            //
        }
    }
}