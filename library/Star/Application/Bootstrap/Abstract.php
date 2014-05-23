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
	protected $class_resources = null;
	
	protected $container = null;
	
	/**
	 * 构造方法
	 */
	public function __construct()
	{
		$this->init();
	}
	
	/**
	 * 初始化方法
	 */
	public function init()
	{
		
	}
	
	/**
	 * 设置配置项
	 *
	 * @param array $options
	 */
	public function setOption($option = null)
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
}

?>