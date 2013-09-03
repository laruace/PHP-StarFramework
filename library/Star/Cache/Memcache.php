<?php
/**
 * Memcach缓存类
 * 
 * @author 
 */


class Star_Cache_Memcache implements Star_Cache_Interface {

	public $memcache = null;
	
	public function __construct(array $config)
	{
		$this->memcache = new Memcache();
		
		if ($config['multi_cache'] == true)
		{
			foreach ((array) $config['server'] as $memcache)
			{
				$this->memcache->addServer($memcache['host'], $memcache['port']);
			}
		} else
		{
			$this->memcache->addServer($config['server']['host'], $config['server']['host']);
		}
	}
	
	public function add($key, $value, $lefttime = 0)
	{
		if ($lefttime == 0)
		{
			return $this->memcache->add($key, $value, false);
		} else
		{
			return $this->memcache->add($key, $value, false, $lefttime);
		}
	}
	
	public function get($key)
	{
		return $this->memcache->get($key);
	}
	
	public function set($key, $value, $lefttime = 0)
	{
		if ($lefttime == 0)
		{
			return $this->memcache->set($key, $value, false);
		} else
		{
			return $this->memcache->set($key, $value, false, $lefttime);
		}
	}
	
	public function delete($key)
	{
		return $this->memcache->delete($key);
	}
    
    public function close()
    {
        if (is_object($this->memcache))
        {
            $this->memcache->close();
        }
    }
	
}

?>