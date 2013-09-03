<?php
/**
 *
 * COOKIE操作类
 *
 * @author zhangqy
 *
 */

class Star_Cookie {
	
    protected static $domain;

    public static function get($name)
    {
        return $_COOKIE[$name];
    }
    
    public static function set($name, $value , $expire = 0, $path = '/', $domain = '')
    {
        empty($domain) && $domain = self::$domain;
        
        setcookie($name, $value, $expire, $path, $domain);
    }
    
    public static function setDomain($domain)
    {
        self::$domain = $domain;
    }
}

?>