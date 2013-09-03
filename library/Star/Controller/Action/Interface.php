<?php

interface Star_Controller_Action_Interface {
	
	public function __construct(Star_Http_Request $reques, Star_View $star_view);
	
	public function initView(Star_View $view);
	
    public function dispatch($action);
}

?>