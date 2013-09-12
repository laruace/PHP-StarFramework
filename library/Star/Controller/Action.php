<?php
/**
 * controller 基础类
 * 
 * @author zhangqinyang
 */

require 'Star/Controller/Action/Interface.php';

class Star_Controller_Action implements Star_Controller_Action_Interface{

	protected $request;
	
	protected $response;
	
	public $view;
	
	public $layout;
	
	final public function __construct(Star_Http_Request $request, Star_View $view)
	{
		$this->setRequest($request);
        $this->initView($view);
        $star_layout = Star_Layout::getMvcInstance();
		
        if ($star_layout instanceof Star_Layout)
        {
            $this->initLayout($star_layout);
        }
        
		$this->init();
	}
	
	public function init()
	{
		
	}
	
    /**
     * 设置REQUEST
     * 
     * @param Star_Http_Request $request
     * @return \Star_Controller_Action 
     */
	protected function setRequest(Star_Http_Request $request)
	{
		$this->request = $request;
		return $this;
	}
	
	public function __call($method_name, $args)
	{
		if (substr($method_name, (strlen($this->request->getActionKey()) * -1)) == ucfirst($this->request->getActionKey()))
		{
			$action = substr($method_name, 0, strlen($method_name) - strlen($this->request->getActionKey()));
			throw new Star_Exception(__CLASS__ . '::' . $action . 'Action not exists', 404);
		}
		throw new Star_Exception(__CLASS__ . '::' . $method_name . ' Method not exists', 500);
	}
	
	public function run()
	{

	}
	
    /**
     * 初始化view
     * 
     * @param Star_View $star_view
     * @return type 
     */
	public function initView(Star_View $star_view)
	{
		$this->view = $star_view;
		$this->view->setController($this->request->getControllerName())
                    ->setScriptName($this->request->getActionName())
                    ->setAction($this->request->getActionName());
		return $this->view;
	}
    
    /**
     * 执行 request action 请求
     * 
     * @param type $action
     */
    public function dispatch($action) 
    {
        $class_methods = get_class_methods($this);
        
        if (in_array($action, $class_methods))
        {
            $this->$action();
            $this->view->loadView();
            
        } else {
            $this->__call($action, array());
        }
    }
	
    /**
     * 初始化 layout
     * 
     * @param Star_Layout $star_layout
     * @return \Star_Controller_Action 
     */
	public function initLayout(Star_Layout $star_layout)
	{
		$this->layout = $star_layout;
		return $this;
	}
	
    /**
     * 关闭layout
     * 
     * @return \Star_Controller_Action 
     */
	protected function disableLayout()
	{
		if ($this->layout instanceof Star_Layout)
		{
			$this->layout->disableLayout();
		}
        
        return $this;
	}
	
    /**
     * 重新指定显示页面
     * 
     * @param type $action
     * @param type $is_controller
     * @return \Star_Controller_Action 
     */
	protected function render($action, $is_controller = true)
	{
        
		$this->view->setScriptName($action, $is_controller)->setRender();
        
        return $this;
	}
	
    /**
     * 页面重定向
     * 
     * @param type $url
     * @return type 
     */
	protected function redirect($url)
	{
		header('Location:' . $url);
		return ;
	}
	
    /**
     * 显示提示信息
     * 
     * @return type 
     */
	protected function showMessage()
	{
		$args = func_get_args();
		$this->view->message = $args[0];
		return $this->render('message', false);
	}
	
    /**
     * 显示警告信息
     * 
     * @return type 
     */
	protected function showWarning()
	{
		$args = func_get_args();
		$this->view->message = $args[0];
		return $this->render('message', false);
	}
	
    /**
     * 显示json数据
     * 
     * @return type 
     */
	protected function showJson()
	{   
		$args = func_num_args();
		$message = array(
			'status' => (bool) $args[0],
			'message' => $args[1],
			'data' => $args[2]
		);

        $this->disableLayout();
        $this->view->setNoRender();
        
		return json_encode($message);
	}
}

?>