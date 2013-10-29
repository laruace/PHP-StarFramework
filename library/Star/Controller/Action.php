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
        if (array_key_exists($action, array_flip($class_methods)))
        {
            $this->$action();
            $this->view->loadView();
            
            //开启layout 加载layout
            if ($this->layout instanceof Star_Layout && $this->layout->isEnabled() == true)
            {
                $body = ob_get_contents();
                ob_clean();
                $this->setLayout($body);
            }

            //判断是否需要更新缓存
            if ($this->view->isCache() == true && $this->view->cacheIsExpire() == true)
            {
                $this->saveViewCache(); //存储页面缓存
            }
        } else {
            $this->__call($action, array());
        }

    }
    
    /**
     * 保存页面缓存 
     */
    private function saveViewCache()
    {
        //开启缓存，且缓存超时或者缓存不存在，则写入缓存
        if ($this->view->isCache() == true && $this->view->cacheIsExpire() == true)
        {
            $this->view->saveCache(ob_get_contents());
        }
        
        return $this->view->isCache();
    }
    
    
    /**
     * 设置layout
     * 
     * @param type $star_view
     * @param type $star_layout
     * @param type $body 
     */
	private function setLayout($body)
	{
		$this->layout->setView($this->view);
		$this->layout->assign($this->layout->getContentKey(), $body);
		$this->layout->render();
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
		$args = func_get_args();
		$message = array(
			'status' => (bool) $args[0],
			'message' => $args[1],
			'data' => $args[2]
		);

        $this->disableLayout();
        $this->view->setNoRender();
        
		return json_encode($message);
	}
    
    protected function setNoRender()
    {
        $this->view->setNoRender();
        
        $this->disableLayout();
    }
    
    protected function getRequest()
    {
        return $this->request;
    }
    
    /**
     * 开启页面缓存 
     */
    protected function openCache($cache_key = '', $timeout = 0, $is_flush = false)
    {
        !empty($cache_key) && $cache_key = $this->getRequest()->getActionName();
        $this->view->openCache($cache_key, $timeout, $is_flush);
    }
    
    /**
     * 是否有页面缓存
     * 
     * @return type 
     */
    protected function isCache()
    {
        return $this->view->cacheIsExpire() == true ? false : true;
    }

    /**
     * 显示页面缓存 
     */
    protected function showCache()
    {
        $this->setNoRender();
        $this->view->loadCache();
    }
    
    /**
     * 强制刷新页面缓存 
     */
    protected function flushCache()
    {
        $this->view->flushCache();
    }
}

?>