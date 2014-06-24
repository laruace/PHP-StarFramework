<?php
/**
 * @package library\Star\Application\Bootstrap
 */

/**
 * @package library\Star\Application\Bootstrap
 * @author zhangqinyang
 */
abstract class Star_Application_Bootstrap_Abstract
{
    protected $application = null; //Star_Application

    protected $class_resources = null;
	
	protected $container = null;
    
    protected $view = null; //Star_View
    
    protected $request = null; //Star_Http_Request
    
    public $front = null; //Star_Controller_Front

    /**
	 * 构造方法
	 */
	public function __construct($application)
	{
        $this->initRequest();
        $this->initResponse();
        $this->initFrontController();
        $this->setApplication($application);
        $options = $application->getOptions();
        $this->setOptions($options);
	}

    /**
     * 设置Application
     * 
     * @param Star_Application $application
     * @return \Star_Application_Bootstrap_Abstract
     * @throws Star_Exception 
     */
    public function setApplication($application)
    {
        if ($application instanceof Star_Application)
        {
            $this->application = $application;
        } else
        {
            throw new Star_Exception('Invalid application provided to bootstrap constructor (received "' . get_class($application) . '" instance)');
        }
        return $this;
    }
    
    /**
     * 返回application
     * @return type 
     */
    public function getApplication()
    {
        return $this->application;
    }


    /**
	 * 设置配置项
	 *
	 * @param array $options
	 */
	public function setOptions($option = null)
	{
		
	}
	
	/**
	 * 
	 */
	public function getClassResources()
	{
		if ($this->class_resources === null)
		{
			if (version_compare(PHP_VERSION, '5.2.6') === -1)
			{
                $class = new ReflectionObject($this);
                
                $class_methods = $class->getMethods();
                
                $methods  = array();

                foreach ($class_methods as $method)
                {
                    $methods[] = $method->getName();
                }
            } else
            {
                $methods = get_class_methods($this);
            }
            
			$this->class_resources = array();
			
			foreach ($methods as $method)
			{
				if (strlen($method) > 5 && substr($method, 0, 5) === '_init')
				{
					$this->class_resources[strtolower(substr($method, 5))] = $method;
				}
			}
		}
		
		return $this->class_resources;
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getClassResourcesName()
	{
		$resources = $this->getClassResources();
		
		return array_keys($resources);
	}
	
	/**
	 * 
	 * @param string $resource
	 * @return NULL
	 */
	protected function excuteResource($resource = null)
	{
  		$resource_name = strtolower($resource);
		$class_resources = $this->getClassResources();
        $return = null;
        
		if (array_key_exists($resource_name, $class_resources))
		{
            $method = '';
			$method = $class_resources[$resource];
			$return = $this->$method();
		}
        return $return;
	}
	
	/**
	 * 
	 * @param string $resource
	 * @return Star_Application_Bootstrap_Abstract
	 */
	final public function bootstrap($resource = null)
	{
		$this->_bootstrap($resource);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $resource
	 */
	protected function _bootstrap($resource = null)
	{
		if ($resource === null)
		{
			$class_resources = $this->getClassResourcesName();

			foreach ($class_resources as $resource)
			{
				$this->excuteResource($resource);
			}
		}
	}
	
	/**
	 * 
	 */
	public function getContainer()
	{
		if ($this->container === null)
		{
			$this->setContainer(new Star_Registry());
		}
	}
	
	/**
	 * 
	 * @param unknown $container
	 * @throws Star_Exception
	 * @return Star_Application_Bootstrap_Abstract
	 */
	public function setContainer($container)
	{
		if (!is_object($container))
		{
			throw new Star_Exception('Resource contrainer must be object.');
		}
		
		$this->container = $container;
		
		return $this;
	}

    /**
     * 初始化view 
     */
    protected function initView()
    {
        if ($this->view == null || ($this->view instanceof Star_View) == false)
        {
            $this->view = new Star_View(array());
        }
    }
    
    /**
     * 消息派遣 
     */
    public function dispatch()
    {
        $this->front->dispatch($this->view);
    }
    
    /**
     * 初始化request 
     */
	protected function initRequest()
	{
        require 'Star/Http/Request.php';
		$request = new Star_Http_Request();
		$this->request = $request;
	}
    
    /**
     * 初始化Response 
     */
    protected function initResponse()
    {
        require 'Star/Http/Response.php';
		$response = new Star_Http_Response();
		$this->response = $response;
    }
    
    /**
     * 初始化frontCrontroller 
     */
    protected function initFrontController()
    {
        $front = new Star_Controller_Front($this->request, $this->response);
        $this->front = $front;
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
		$this->view = new Star_View($this->getApplication()->getApplicationPath(), $options);
		
		return $this;
	}
    
    /**
     * 设置缓存
     * 
     * @param array $options 
     */
	public function setCache(array $options)
	{
        require 'Star/Cache.php';
        Star_Cache::initInstance($options);
	}
    
    /**
     * 设置DB
     * 
     * @param type $options 
     */
    public function setDb($options)
    {
        call_user_func(array('Star_Model_Abstract', 'setting'), $options);
        return $this;
    }
    
    /**
     * 设置FrontController
     * 
     * @param type $options 
     */
    public function setFrontController($options)
    {
        $this->front->setOptions($options);
        return $this;
    }
}

?>