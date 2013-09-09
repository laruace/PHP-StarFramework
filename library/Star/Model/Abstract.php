<?php

/**
 *
 * @author qinyang.zhang
 * @create date 2010/05/27
 */

abstract class Star_Model_Abstract
{
	
	const ADAPTER = 'db';
	
	const SLAVE_ADAPTER = 'slave_db';
	
	protected $_primary = null; //主键
	
	protected $_prefix = null; //表前缀
	
	protected static $_config = null; //配置
	
	protected static $_db = null;
	
	protected $_name = null; //表名称
	
	protected static $_default_db = null;
	
	protected $_support_db = array('mysqli', 'mysql');
	
	public function __construct($config = array())
	{
		if (is_string($config))
		{
			$config = array(self::ADAPTER => $config);
		}
		
		if (!empty($config) && is_array($config))
		{
			$this->setOptions($config);
		}
		
		$this->_setup();
	}
	
	private function _setup()
	{
		if (self::$_default_db != null)
		{
			return ;
		}
		
		$config = self::$_config;
		$this->setDefaultAdapter($config[self::ADAPTER]);
		$this->setAdapter($this->getSlaveDbOption());

		if (self::$_db === null)
		{
			self::$_db = & self::$_default_db;
		}
	}
	
	protected function getSlaveDbOption()
	{
		$config = self::$_config;
		
		$slave_options = $config[self::SLAVE_ADAPTER];
		$option = array();
		
		if ($config[self::ADAPTER]['multi_slave_db'] == true && !empty($slave_options))
		{
			$option = $slave_options[array_rand($slave_options)];
		} else
		{
			$option = $slave_options;
		}

		return $option;
	}
	
	/**
	 * 插入数据
	 *
	 * @param array $data
	 */
	public function insert(array $data)
	{
		return  self::$_default_db->insert($this->getTableName(), $data);
	}

	/**
	 * 更新数据
	 *
	 * @param $where
	 * @param array $data
	 */
	public function update($where, Array $data, $quote_indentifier = true)
	{
		$where = $this->setWhere($where);
		
		return self::$_default_db->update($this->getTableName(), $where, $data, $quote_indentifier);
	}
	
	/**
	 * 删除数据
	 *
	 * @param $where
	 */
	public function delete($where)
	{
		$where = $this->setWhere($where);
		
		return self::$_default_db->delete($this->getTableName(), $where);
	}
	
	public function query($sql)
	{
		return self::$_db->query($sql);
	}
    
    /**
     * 返回相对应主键信息
     * 
     * @param type $pk_id
     * @return type 
     */
    public function getPk($pk_id)
    {
        $where = $this->setWhere($pk_id);
        
        return self::$_db->fetchRow($where, '*', $this->getTableName());
    }
	
    /**
     *
     * @param type $where
     * @return type 
     */
	public function fetchOne($where)
	{
		return self::$_db->fetchOne($where);
	}
	
	public function fetchAll($where)
	{
		return self::$_db->fetchAll($where);
	}
	
	public function fetchRow($where)
	{
		return self::$_db->fetchRow($where);
	}
	
	public function fetchCol($where)
	{
		$rs = self::$_db->fetchAll($where);
		
		$data = array();

		if (!empty($rs) && is_array($rs))
		{
			foreach ($rs as $value)
			{
				$data[] = $value[$this->_primary];
			}
		}
		
		return $data;
	}
	
	public function select()
	{ 
		return self::$_db->select();
	}
	
	/**
	 * 返回表明
	 */
	public function getTableName()
	{
		return $this->_prefix . $this->_name;
	}
	
    /**
     * 关闭数据库连接 
     */
	public static function Close()
	{
		if (self::$_db !== self::$_default_db)
		{
			self::$_db->close();
		}

		if (self::$_default_db !== null)
		{
			self::$_default_db->close();
		}
	}
	
    /**
     * 设置适配器
     * 
     * @param type $db
     * @return null|\adapter 
     */
	protected function setupAdapter($db)
	{
		if ($db == null || !is_array($db))
		{
			return null;
		}

		if (is_array($db) && in_array(strtolower($db['adapter']), $this->_support_db))
		{
			$adapter = ucfirst($db['adapter']);
            require_once "Star/Model/{$adapter}/Abstract.php";
			$adapter = 'Star_Model_' . $adapter . '_Abstract';
			return new $adapter($db['params']);
		}
	}
	
	public function setAdapter($db)
	{
		if (empty($db) || !is_array($db))
		{
			return $db;
		}
		
		return self::$_db = self::setupAdapter($db);
	}
	
	public function getAdapter()
	{
		return self::$_db;
	}
	
	public function setOptions(Array $options)
	{
		foreach ($options as $key => $value)
		{
			switch ($key)
			{
				CASE self::ADAPTER:
					$this->setDefaultAdapter($value);
					break;
				CASE self::SLAVE_ADAPTER :
					$this->setAdapter($value);
					break;
                default :
                    break;
			}
		}
	}
	
	public static function setting($db)
	{
		self::$_config = $db;
	}
	
	public function getDefaultAdapter($config = null)
	{
		return self::$_default_db;
	}
	
	/**
	 * 设置默认适配器
	 *
	 * @param  $db
	 */
	public function setDefaultAdapter($db)
	{
		return self::$_default_db = self::setupAdapter($db);
	}
	
	/**
     * object to array
     * return array
     */
    public function objectToArray($object, $fields = array(), $flag=false)
    {
    	$data = get_object_vars($object);
    	$arr = array();
    	foreach ($data as $key => $value)
    	{
    		if (strtoupper($key)==strtoupper($this->_primary))
    		{
    			continue;
    		}
    		if (!empty($fields))
    		{
    			if (in_array($key, $fields))
    			{
    				$arr[$key] = $value;
    			}
    		} else
    		{
    			$arr[$key] = $value;
    		}
    	}
    	unset($data);
    	if ($flag == true)
    	{
    		$arr = array_change_key_case($arr, CASE_UPPER);
    	}
    	return $arr;
    }
    
    private function setWhere($where)
    {
    	if (is_numeric($where))
    	{
    		$where = $this->_primary . ' = ' . $where;
    	}
    	
    	if (is_array($where))
    	{
    		$where = $this->_primary . ' IN (' . implode(',', $where) . ')';
    	}
    	
    	return $where;
    }
}

?>