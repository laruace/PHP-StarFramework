<?php
/**
 *
 * String类
 *
 * @author zhangqy
 *
 */

class Star_String {
	
	public static function escape($str)
    {
        $str = trim($str); //去除两边空字符
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        $str = htmlspecialchars($str, ENT_QUOTES); //
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
    
    public static function htmlspecialchars($str)
    {
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        return htmlspecialchars($str, ENT_QUOTES);
    }
    
    public static function htmlspecialchars_decode($str)
    {
        return htmlspecialchars_decode($str, ENT_QUOTES);
    }
    
    public static function deepStripslashes($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = self::deepStripslashes($value);
			}
		} else if (is_string($data))
		{
			$data = stripcslashes($data);
		}
		
		return $data;
	}
    
    /**
     * 返回数字
     * 
     * @param type $number
     * @return type 
     */
    public static function numeric($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }
}

?>