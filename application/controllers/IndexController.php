<?php

class IndexController extends Star_Controller_Action
{
    public function init()
	{
		
	}
	
	public function indexAction()
	{
        //$user_service = new UserService();
        $this->view->openCache();
        
        $this->view->loadCache();

        $user_service = new UserService();
        
        var_dump($user_service->getUserByPage(1, 1));
        
        $this->view->assign('title', 'Hello world!');
        
        $this->view->setJsConfig(array(
            'files' => array('jquery')
        ));
        echo time();
        //$str = "fjakldjflakg'ja'gjqpoigjq;k<>faf<a href=www.baidu.com>www.baidu.com</a>";

        //echo Star_Filter::escape($str);

        //echo date('Y-m-d H:i:s', Star_Date::getLastWeek());
        echo "Hello world";
	}
	
	public function helloAction()
	{
        $this->view->title = 'Hello world';
        //$this->view->setNoRender();
	}
    
}

?>