<?php
/**
 * @package library\Star\Http
 */

/**
 * http抽象类
 * 
 * @package library\Star\Http
 * @author zhangqy
 *
 */
abstract class Star_Http_Abstract
{
	protected $controller_name = '';
	
	protected $action_name = '';
    
    protected $default_controller_name = 'index';

    protected $default_action_name = 'index';

    protected $controller_key = 'Controller';
    
    protected $action_key = 'Action';
    	
	public function __construct()
	{
		
	}
    
    public function getControllerName()
    {
        return $this->controller_name ? $this->controller_name : $this->default_controller_name;
    }
    
    public function getActionName()
    {
        return $this->action_name ? $this->action_name : $this->default_action_name;
    }
    
    public function getControllerKey()
    {
        return $this->controller_key;
    }

    public function getActionKey()
    {
        return $this->action_key;
    }

    /**
     * 设置默认controller
     * 
     * @param type $controller 
     */
	public function setDefaultControllerName($controller_name = 'index')
	{
        $this->default_controller_name = $controller_name;
        return $this;
	}
	
    /**
     * 设置默认action
     * 
     * @param type $action 
     */
	public function setDefaultActionName($action_name = 'index')
	{
        $this->default_action_name = $action_name;
        
        return $this;
	}

    /**
     * 设置当前controller
     * 
     * @param type $controller_name 
     */
	public function setControllerName($controller_name)
	{
		!empty($controller_name) && $this->controller_name = $controller_name;
        
        return $this;
	}
	
    /**
     * 设置当前action
     * 
     * @param type $action_name 
     */
	public function setActionName($action_name)
	{
		!empty($action_name) && $this->action_name = $action_name;

        return $this;
	}
	
    /**
     * 返回当前controller
     * 
     * @return type 
     */
	public function getController()
	{
        empty($this->controller_name) && $this->controller_name = $this->default_controller_name;
        
        $controller = ucfirst($this->controller_name) . ucfirst($this->controller_key);
        
		return $controller;
	}
	
    /**
     * 返回当前action
     * 
     * @return type 
     */
	public function getAction()
	{
        empty($this->action_name) && $this->action_name = $this->default_action_name;
        
        $action = strtolower($this->action_name) . ucfirst($this->action_key);
        
		return $action;
	}
    
    public function setControllerKey($controller_key)
    {
        //如果controller_key不为空，则修改controller_key
        if ($controller_key)
        {
            $this->controller_key = trim($controller_key);
        }
        
        return $this;
    }
    
    public function setActionKey($action_key)
    {
        $action_key && $this->action_key = trim($action_key);
        
        return $this;
    }
    
}

?>