<?php

class UserModel extends Star_Model_Abstract
{
	protected $_name = 'qzone_recharge';
	
	protected $_primary = 'recharge_id';
	
	public function getUserById()
	{
		$select = $this->getAdapter()->select();
		
		$select->from($this->getTableName())
			   ->where('recharge_id > ?', "1")
               ->orWhere('recharge_id < ?', 100)
			   ->limitPage(1, 20);

		return $this->fetchAll($select);
	}
	
	public function getUserCount()
	{
		$select = $this->getAdapter()->select();

		$select->from($this->_name, 'count(*) number');

		return $this->fetchOne($select);
	}
}

?>