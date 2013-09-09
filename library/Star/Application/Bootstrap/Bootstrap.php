<?php
/**
 * bootstrap 类
 * 
 * 用于应用启动初始化 
 * 
 * 
 * @author
 * 
 */

require 'Star/Application/Bootstrap/Abstract.php';

class Star_Application_Bootstrap_Bootstrap extends Star_Application_Bootstrap_Abstract
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function init()
	{
		$this->setOption();
		
	}
	
	public function setOption($option = null)
	{
		
	}

}

?>