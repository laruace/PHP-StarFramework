<?php

abstract class Star_Application_Bootstrap_Abstract
{
	protected $class_resources = null;
	
	protected $container = null;
	
	public function __construct()
	{
		$this->init();
	}
	
	public function init()
	{
		
	}
	
	public function setOption($option = null)
	{
		
	}
	
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
	
	public function getClassResourcesName()
	{
		$resources = $this->getClassResources();
		
		return array_keys($resources);
	}
	
	protected function excuteResource($resource = null)
	{
  		$resource_name = strtolower($resource);
		
		$class_resources = $this->getClassResources();

		if (array_key_exists($resource_name, $class_resources))
		{
			$method = $class_resources[$resource];

			$return = $this->$method();
		}
	}
	
	final public function bootstrap($resource = null)
	{
		$this->_bootstrap($resource);
		
		return $this;
	}
	
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
	
	public function getContainer()
	{
		if ($this->container === null)
		{
			$this->setContainer(new Star_Registry());
		}
	}
	
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