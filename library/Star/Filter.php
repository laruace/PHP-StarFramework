<?php
/**
 *
 * Filter类
 *
 * @author zhangqy
 *
 */

class Star_Filter {
	
	public static function escape($str)
    {
        $str = trim($str); //去除两边空字符
        $str = htmlentities($str, ENT_QUOTES); //
        return $str;
    }
    
    public static function stripTags($str)
    {
        return strip_tags($str);
    }
    
    public static function stripcslashes($str)
    {
        return stripcslashes($str);
    }
    
    public static function addcslashes($str)
    {
        return addcslashes($str);
    }
    
    public static function urlDecode($str)
    {
        return rawurldecode($str);
    }
    
    public static function urlEncode($str)
    {
        return rawurlencode($str);
    }
    
    public static function jsonEncode($data)
    {
        return json_encode($data);
    }
    
    public static function jsonDecode($data)
    {
        return json_decode($data, true);
    }
    
    public static function strLength($str)
    {
        $str = trim(htmlspecialchars($str, ENT_QUOTES));
        
        return mb_strlen($str, 'utf-8');
    }
    
    public static function substr($str, $start, $end)
    {
        $str = mb_substr($str, $start, $end, 'utf-8');
        return htmlentities($str, ENT_QUOTES);
    }
}

?>