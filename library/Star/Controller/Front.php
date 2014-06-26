<?php
/**
 * @package library\Star\Controller\Front
 */
/**
 * 导入文件
 */

/**
 * controller front类
 * 
 * @package library\Star\Controller\Front
 * @author zhangqinyang
 */
class Star_Controller_Front{

    protected $display_exceptions = false; //默认不显示异常

    protected $request;
	
	protected $response;
    
    protected $view;

    protected $controller_directory;

    protected $controller_name = '';
	
	protected $action_name = '';
    
    protected $default_controller_name = 'index';

    protected $default_action_name = 'index';

    protected $controller_key = 'Controller';
    
    protected $action_key = 'Action';
    	
	public function __construct(Star_Http_Request $request, Star_Http_Response $response)
	{
		$this->request = $request;
        $this->response = $response;
        $this->setActionName($this->request->getActionName());
        $this->setControllerName($this->request->getControllerName());

	}
    
    /**
     * 设置options
     * 
     * @param type $options
     * @return \Star_Controller_Front 
     */
    public function setOptions($options = array())
    {
        if (!empty($options))
        {
            $this->options = $options;
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
        return $this;
    }
    
    /**
     * 设置view
     * 
     * @param Star_View $view
     * @return $this 
     */
    public function setView(Star_View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * 返回controller name
     * 
     * @return type 
     */
    public function getControllerName()
    {
        return $this->controller_name ? $this->controller_name : $this->default_controller_name;
    }
    
    /**
     * 返回action name
     * 
     * @return type 
     */
    public function getActionName()
    {
        return $this->action_name ? $this->action_name : $this->default_action_name;
    }
    
    /**
     * 返回controller key
     * 
     * @return type 
     */
    public function getControllerKey()
    {
        return $this->controller_key;
    }
    
    /**
     * 返回action key
     * 
     * @return type 
     */
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
    
    /**
     * 设置controller key
     * 
     * @param type $controller_key
     * @return \Star_Controller_Front 
     */
    public function setControllerKey($controller_key)
    {
        //如果controller_key不为空，则修改controller_key
        if ($controller_key)
        {
            $this->controller_key = trim($controller_key);
        }
        
        return $this;
    }
    
    /**
     * 设置action key
     * 
     * @param type $action_key
     * @return \Star_Controller_Front 
     */
    public function setActionKey($action_key)
    {
        $action_key && $this->action_key = trim($action_key);
        
        return $this;
    }
    
    /**
     * 消息派遣 调用控制器
     * 
     * @return type 
     */
	public function dispatch()
	{
        header('Cache-Control: no-cache');
		header('Content-Type: text/html; charset=' . $this->view->getEncoding());
		ob_start();
        
        try{
            require 'Star/Controller/Action.php';
            $this->view->setController($this->getControllerName())
                 ->setScriptName($this->getActionName())
                 ->setAction($this->getActionName());
            $controller = $this->loadController(); //实例化controller
            $action = $this->getAction(); //返回action
            $controller->dispatch($action); //执行action
            call_user_func(array('Star_Model_Abstract', 'Close')); //主动关闭数据库链接
        } catch (Exception $e)
        {
            call_user_func(array('Star_Model_Abstract', 'Close')); //主动关闭数据库链接
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
        $contoller = $this->getController();
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

        return new $contoller($this->request, $this->response, $this->view);
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
            $directory_name = Star_Loader::getLoadTypeByKey($this->getControllerkey());
            $this->controller_directory = Star_Loader::getModuleDirect($directory_name);
        }
        
        return $this->controller_directory;
    }
    
    /**
     * 设置是否显示异常
     * 
     * @param type $flag
     * @return type 
     */
    protected function setDisplayExceptions($flag = false)
    {
        return $this->display_exceptions = $flag;
    }
    
    /**
     * 返回异常显示状态
     * 
     * @return type 
     */
    protected function getDisPlayExceptions()
    {
        return $this->display_exceptions;
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

        if ($this->getDisPlayExceptions() == true)
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

?>