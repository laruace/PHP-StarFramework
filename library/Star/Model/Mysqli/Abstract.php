<?php

class Star_Model_Mysqli_Abstract implements Star_Model_Interface
{
	protected $db;
	
	protected $default_charset = 'utf8';
	
    protected $slow_query_log = false; //默认关闭慢查询日志

    protected $slow_query_time = 2; //慢查询默认时间

    protected $statement = null;
	
	public function __construct($config)
	{
		$this->init($config);
	}
    
    protected function init($config)
    {
        if (isset($config['slow_query_log']))
        {
            $this->slow_query_log = $config['slow_query_log'];
        }
        
        if (isset($config['slow_query_time']) && !empty($config['slow_query_time']))
        {
            $this->slow_query_time = $config['slow_query_time'];
        }
        
        $this->connect($config);
    }

    public function connect($db)
	{
        if (!extension_loaded('mysqli'))
        {
            throw new Star_Exception('The Mysqli extension is required for this adapter but the extension is not loaded');
        }
        
		extract($db);

		$this->db = new mysqli($host, $username, $password, $dbname);
		
		if ($this->db->connect_error)
		{
			throw new Star_Exception('Mysqli connet error:(' . $this->db->connect_errno . ')' . $this->db->connect_error, 500);
		}
        
        $this->db->set_charset($this->default_charset);
	}
	
	public function beginTransaction()
	{
		$this->db->autocommit(false);
	}
	
	/**
	 * 插入数据
	 * @param $data
	 */
	public function insert($table, Array $data)
	{
		$columns = array_keys($data); //表字段
		
		$columns = $this->quoteIdentifier($columns);
		
		$data = $this->quoteIdentifier($data, true);
		
		$sql = 'INSERT INTO ' . $this->quoteIdentifier($table) . '(' . implode(',', $columns) . ') VALUES (' . implode(',', $data) . ')';
		
		$this->_query($sql);
		
		return $this->db->insert_id;
	}
	
	/**
	 * 更新数据
	 *
	 * @param $where
	 * @param $data
	 */
	public function update($table, $where, Array $data, $quote_indentifier = true)
	{
		
		foreach ($data as $column => &$value)
		{
            $value = $this->quoteIdentifier($column) . ' = ' .  ($quote_indentifier == true ? $this->quoteIdentifier($value, true) : $value);
		}
		
		$sql = 'UPDATE ' . $this->quoteIdentifier($table) . ' SET ' . implode(',', $data) . ' WHERE ' . $where;

		$this->_query($sql);
		
		return $this->rowCount();
		
	}
	
	/**
	 * 删除数据
	 * @param $where
	 */
	public function delete($table, $where)
	{
		$sql = 'DELETE FROM ' . $table . ' WHERE ' . $where;
		
		$this->_query($sql);
		
		return $this->rowCount();
	}
    
    /**
     * 根据条件生成SQL
     * 
     * @param type $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @param type $page
     * @param type $page_size
     * @return type 
     */
    protected function getSql($where, $conditions, $table, $order = null, $page = 1, $page_size = 1)
    {
        $select = $this->select();
        
        $select->from($table, $conditions)->where($where);
        
        if ($order !== null)
        {
            $select->order($order);
        }
        
        if ($page !== null && $page_size !== null)
        {
            $select->limitPage($page, $page_size);
        }
        
        return $select->assemble();
    }


    /**
	 * 返回结果集
	 * @param $select
	 */
	public function fetchAll($where, $conditions = null, $table = null, $order = null, $page = null, $page_size = null)
	{
        $sql = '';
        
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$sql = $where->assemble();
		} else
        {
            $sql = $this->getSql($where, $conditions, $table, $order, $page, $page_size);
        }
		
		$result = $this->_query($sql);
		
        
        $data = array();
		
		while ($rs = $result->fetch_assoc())
		{
			$data[] = $this->deepStripslashes($rs);
		}
		
		$result->free();
		
		return $data;
	}
	
	/**
	 * 返回结果
	 * @param $id
	 */
	public function fetchOne($where, $conditions = null, $table = null, $order = null)
	{
        $sql = '';
        
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$where->limit(1);
			
			$sql = $where->assemble();
		} else {
            $sql = $this->getSql($where, $conditions, $table, $order);
        }

		 $result = $this->_query($sql);
	
		$data = $result->fetch_assoc();
		
		return is_array($data) ? current($data) : '';
	}
	
	/**
	 * 返回一行结果集
	 * @param $select
	 */
	public function fetchRow($where, $conditions = null , $table = null)
	{
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$where->limit(1);
			
			$sql = $where->assemble();
		} else {
            $sql = $this->getSql($where, $conditions, $table);
        }
		
		$result = $this->_query($sql);
		
		$data = $result->fetch_assoc();
		
		return $data = $this->deepStripslashes($data);
	}
	
	public function fetchCol($where, $conditions = null , $table = null)
	{
		$data = $this->fetchAll($where, $conditions = null , $table = null);
		
		return $this->deepStripslashes($data);
	}
    
    public function query($sql)
    {
        $result = $this->_query($sql);
     
        $data = array();
        
        while ($rs = $result->fetch_assoc())
		{
			$data[] = $this->deepStripslashes($rs);
		}
        
        return $data;
    }
	
	/**
	 * 返回影响行数
	 */
	public function rowCount()
	{
		return $this->db->affected_rows;
	}
	
	/**
	 * sql���
	 * @param $sql
	 */
	public function _query($sql)
	{
        if ($this->slow_query_log == true)
        {
            $start_time = time();
        }
        
		$resource = $this->db->query($sql);
        
        if (!is_object($resource))
        {
            throw new Star_Exception("sql err: ". $sql, 500);
        }

        if($this->slow_query_log == true)
        {
            $end_time = time();

            $time = $end_time - $start_time;

            if ($time >= $this->slow_query_time) //慢查询日志
            {
                $stact_trace = Star_Debug::Trace(); //返回堆栈详细信息
                
                $stact_trace = implode("\n", $stact_trace);

                Star_Log::log("Query_time: {$time}s     Slow query: {$sql} \nStack trace:\n{$stact_trace}", 'slow_query'); //记录慢查询日志
            }
        }
        
        return $resource;
	}
    
    public function select()
	{
		return new Star_Model_Mysqli_Select();
	}
	
	public function close()
	{
		return $this->db->close();
	}
	
	/**
	 * 字符串引号
	 *
	 * @param $identifier
	 * @param $auto_quote
	 */
	public function quoteIdentifier($identifier, $auto_quote = false)
	{
		if (is_array($identifier))
		{
			foreach ($identifier as &$value)
			{
				if ($auto_quote == true)
				{
					$value = "'" . addslashes($value) . "'";
				} else
				{
					$value = '`' . $value . '`';
				}
			}
		} else
		{
			if ($auto_quote == true)
			{
				$identifier = "'" . addslashes($identifier) . "'";
			} else
			{
				$identifier = '`' . $identifier . '`';
			}
		}
		
		return $identifier;
	}
	
	protected function deepStripslashes($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = $this->deepStripslashes($value);
			}
		} else if (is_string($data))
		{
			$data = stripcslashes($data);
		}
		
		return $data;
	}
	
	public function commit()
	{
		return $this->db->commit();
	}
	
	public function rollback()
	{
		return $this->db->rollback();
	}

}
?>