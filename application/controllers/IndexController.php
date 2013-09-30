<?php

class IndexController extends Star_Controller_Action
{
    public function init()
	{
		
	}
	
	public function indexAction()
	{
        Star_Http_Response::setBrownerCache(600);
        
        //$user_service = new UserService();
        
        //$user_service->getUserByPage(1, 10);
        $this->openCache('', 0);
        if ($this->isCache())
        {
            return $this->showCache();
        }
        
        //$this->view->assign('title', 'Hello world!');
        
        //$this->view->setJsConfig(array(
        //    'files' => array('jquery')
        //));
        echo date('Y-m-d H:i:s');
        //$str = "fjakldjflakg'ja'gjqpoigjq;k<>faf<a href=www.baidu.com>www.baidu.com</a>";

        //echo Star_Filter::escape($str);
        //$this->view->assign(array('title' => 'Hello world', 'data' => 'fadjfakl'));
        //echo date('Y-m-d H:i:s', Star_Date::getLastWeek());
        //echo "Hello world";
        
        //$this->view->setNoRender();
        //$this->render('index');
	}
	
	public function helloAction()
	{
        $this->view->title = 'Hello world';
        //$this->view->setNoRender();
	}
    
}

?>