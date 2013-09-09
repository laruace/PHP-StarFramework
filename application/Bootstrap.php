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
        return ;
		Star_Layout::startMvc(array(
			'base_path' => APPLICATION_PATH . '/layouts',
			'script_path' => 'default',
		));
	}
	
	protected function _initConfig()
	{
        return ;
		Star_Registry::set('ddd', 123);
	}
	
}

