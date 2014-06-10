<?php
/**
 * @package library\Star\Application\Bootstrap
 */

/**
 * 导入文件
 */
require 'Star/Application/Bootstrap/Abstract.php';

/**
 * bootstrap 类
 * 
 * 用于应用启动初始化 
 * @package library\Star\Application\Bootstrap 
 * @author zhangqinyang
 * 
 */
class Star_Application_Bootstrap_Bootstrap extends Star_Application_Bootstrap_Abstract
{
    protected $request = null;

    /**
	 * 构造方法
	 */
	public function __construct($request)
	{
        $this->request = $request;
		parent::__construct();
	}
	
	/**
	 * 初始化
	 */
	public function init()
	{
		$this->setOption();
		
	}
	
	/**
	 * 设置配置项
	 *
	 * @param array $options
	 */
	public function setOption($option = null)
	{
		
	}

}

?>