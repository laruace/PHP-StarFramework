<?php

require 'Star/Application/Bootstrap/Bootstrap.php';

class Bootstrap extends Star_Application_Bootstrap_Bootstrap
{
    
	protected function _initSession()
	{
		//print_r(Star_Registry::get());
	}

	protected function _initView()
	{
		Star_Layout::startMvc(array(
			'base_path' => APPLICATION_PATH . '/layouts',
			'script_path' => 'default',
		));
	}

    protected function _initHttpCache()
    {
        if (Star_Http_Request::isCache() == true)
        {
            header('Cache-control: private');
            header(Star_Http_Response::getCodeMessage(304));
            exit;
        }
    }

    protected function _initConfig()
	{
        return ;
	}
	
}

