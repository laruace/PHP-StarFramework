<?php
/**
 *
 * 缓存
 *
 * @author zhangqy
 *
 */

class Star_Cache {
	
	protected static $instance = null;
	
	protected $cache_types = array('Memcache', 'File', 'Memcached', 'Redis'); //缓存类型
	
	public $cache = null;
    
    public $config = array();

    /**
     * 构造函数
     * 
     * @param array $config 
     */
    protected function __construct(array $config)
	{
		$this->config = $config;
	}
	
    /**
     *    
     * @param array $config
     * @return type
     * @throws Star_Exception 
     */
	public function factory(array $config)
	{
		$cache_type = ucfirst($config['type']);
		
		$cache_class = "Star_Cache_{$cache_type}";

		if (!in_array($cache_type, $this->cache_types))
		{
			throw new Star_Exception( $cache_type . ' cache type isn\'t supported.');
			
			return ;
		}

		return new $cache_class($config);
	}
        
    /**
     *
     * @return type 
     */
    public function getCache()
    {
        if (self::$instance->cache === null)
        {
            self::$instance->cache = self::$instance->factory(self::$instance->config);
        }
        
        return self::$instance->cache;
    }
	
	public static function initInstance(array $config)
	{
		if (self::$instance == null)
		{
			self::$instance = new self($config);
		}
		
		return self::$instance;
	}
	
    /**
     * 添加缓存
     * 
     * @param type $key
     * @param type $value
     * @param type $lefttime
     * @return type 
     */
	public static function add($key, $value, $lefttime = 0)
	{
		return self::getCache()->add($key, $value, (int) $lefttime);
	}
	
    /**
     * 根据key返回缓存值
     * 
     * @param type $key
     * @return type 
     */
	public static function get($key)
	{
		return self::getCache()->get($key);
	}
	
    /**
     * 重置缓存值
     * 
     * @param type $key
     * @param type $value
     * @param type $lefttime
     * @return type 
     */
	public static function set($key, $value, $lefttime = 0)
	{
		return self::getCache()->set($key, $value, (int) $lefttime);
	}
	
    /**
     * 根据key删除缓存值
     * 
     * @param type $key
     * @return type 
     */
	public static function delete($key)
	{
		return self::getCache()->delete($key);
	}
	
	public static function lPush($key, $value)
	{
		return self::getCache()->lPush($key, $value);
	}

	public static function rPush($key, $value)
	{
		return self::getCache()->rPush($key, $value);
	}

	public static function lPop($key)
	{
		return self::getCache()->lPop($key);
	}

	public static function rPop($key)
	{
		return self::getCache()->rPop($key);
	}

	public static function lSize($key)
	{
		return self::getCache()->lSize($key);
	}

	public static function lRange($key, $start, $end)
	{
		return self::getCache()->lRange($key, $start, $end);
	}
}

?>