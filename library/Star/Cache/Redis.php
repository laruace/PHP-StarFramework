<?php

class Star_Cache_Redis implements Star_Cache_Interface {

	public $redis = null;
	
	public function __construct(array $config)
	{
		$this->redis = new Redis();
        
        $this->redis->connect($config['host'], $config['port']);
	}
	
	public function add($key, $value, $lefttime = 0)
	{
		if ($lefttime > 0)
        {
            return $this->redis->setex($key, $lefttime, $value);
        } else
        {
            return $this->redis->set($key, $value);
        }
	}
	
	public function get($key)
	{
		return $this->redis->get($key);
	}
	
	public function set($key, $value, $lefttime = 0)
	{
        if ($lefttime > 0)
        {
            return $this->redis->setex($key, $lefttime, $value);
        } else
        {
            return $this->redis->set($key, $value);
        }
	}
	
	public function delete($key)
	{		
		return $this->redis->del($key);
	}

	public function rPush($key, $value)
	{
		return $this->redis->rPush($key, $value);
	}

	public function lPush($key, $value)
	{
		return $this->redis->lPush($key, $value);
	}
	
	public function lPop($key)
	{
		return $this->redis->lPop($key);
	}

	public function rPop($key)
	{
		return $this->redis->rPop($key);
	}

	public function lSize($key)
	{
		return $this->redis->lSize($key);
	}

	public function lRange($key, $start, $end)
	{
		return $this->redis->lRange($key, $start, $end);
	}
}

?>