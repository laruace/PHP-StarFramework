<?php
/**
 * Memcached 缓存类
 * 
 * @author zhangqinyang
 * 
 */

class Star_Cache_Memcached implements Star_Cache_Interface {

	public $memcached = null;
	
	public function __construct(array $config)
	{
		$this->memcached = new Memcached();
		
		if ($config['multi_cache'] == true)
		{
			$this->memcached->addServers($config['server']);
			
			$this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
			
			$this->memcached->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
		} else
		{
			$this->memcached->addServer($config['server']['host'], $config['server']['host']);
		}
	}
	
	protected function getServerByKey($key)
	{
		return $this->memcached->getServerBykey($key);
	}
	
	public function add($key, $value, $lefttime = 0)
	{
		if ($lefttime == 0)
		{
			return $this->memcached->add($key, $value);
		} else
		{
			return $this->memcached->add($key, $value,  $lefttime);
		}
	}
	
	public function get($key)
	{
		return $this->memcached->get($key);
	}
	
	public function set($key, $value, $lefttime = 0)
	{
		if ($lefttime == 0)
		{
			return $this->memcached->set($key, $value);
		} else
		{
			return $this->memcached->set($key, $value, $lefttime);
		}
	}
	
	public function delete($key)
	{
		return $this->memcached->delete($key);
	}
	
    public function colse()
    {
      
    }
}

?>