<?php
/**
 * @package library\Star\Controller\Action
 */

/**
 * 控制器 接口
 *
 * @package library\Star\Controller\Action
 * @author zhangqy
 *
 */
interface Star_Controller_Action_Interface {
	
	public function __construct(Star_Http_Request $reques, Star_View $star_view);
	
	public function initView(Star_View $view);
	
    public function dispatch($action);
}

?>