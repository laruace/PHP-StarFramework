<?php

class UserService {

	protected $user_model;

	public function __construct()
	{
		$this->user_model = new UserModel();
        
        //$member_model = new MemberModel();
        
        //print_r($member_model->get());
	}
	
	public function getUserByPage($page, $page_size)
	{
		//$this->user_model->getDefaultAdapter()->beginTransaction();
		
		//var_dump($this->user_model->update('uid = 10041692', array('is_assistant' => 1, 'nickname' => '333"&^哈哈')));
		
		//$this->user_model->getDefaultAdapter()->rollback();
		
		//$total = $this->user_model->getUserCount();
		
        //$this->user_model->insert(array('token' => 'fjalkdfj', 'billno' => 'fhalffajkl'));
		
		$users = $this->user_model->getUserById();
		
		$page_data = array('total' => $total, 'page' => $page);
        
		$page = Page::show($page_data);
		
		return array('users' => $users, 'page' => $page);
	}

}

?>