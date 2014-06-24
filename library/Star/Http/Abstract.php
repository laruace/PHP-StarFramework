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


    public function __construct()
	{
		
	}
    
    public function getControllerName()
    {
        return $this->controller_name;
    }
    
    public function getActionName()
    {
        return $this->action_name;
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
}

?>