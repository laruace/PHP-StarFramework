<?php

/**
 * 全局变量
 *
 * @author zhangqy
 *
 */


class Star_Registry {

	private static $registry = null;
	
	public static function set($key, $value)
	{
		self::$registry[$key] = $value;
	}
	
	public static function get($key = '')
	{
		$options = self::$registry;
		
		return !empty($key) ? $options[$key] : $options;
	}
	
	public static function delete($key = '')
	{
		if (!empty($key))
		{
			unset(self::$registry[$key]);
		}
	}
	
    public static function isRegistry($key)
    {
        return isset(self::$registry[$key]);
    }


    public static function destroy()
	{
		self::$registry = null;
	}
}

?>