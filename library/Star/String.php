<?php
/**
 * @package library\Star
 */

/**
 *
 * String类
 * 
 * @package library\Star
 * @author zhangqy
 *
 */
class Star_String {
	
	/**
	 * html特殊字符转义，如(double quote) becomes '&quot;' (single quote) becomes '&#039;' 
	 * @param string $str
	 * @return string
	 */
	public static function escape($str)
    {
        $str = trim($str); //去除两边空字符
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        $str = htmlspecialchars($str, ENT_QUOTES); //
        return $str;
    }
    
    /**
     * 除去html标签  
     * 例: echo strip_tags("Hello <b>world!</b>");  -> Hello world!
     * @param string $str
     * @return string
     */
    public static function stripTags($str)
    {
        return strip_tags($str);
    }
    
    /**
     * 删除由 addcslashes() 函数添加的反斜杠。
     * 例: echo stripcslashes("Hello, \my na\me is Kai Ji\m."); -> Hello, my name is Kai Jim.
     * @param string $str
     * @return string
     */
    public static function stripcslashes($str)
    {
        return stripcslashes($str);
    }
    
    /**
     * 指定字符添加反斜杠
     * @param $str
     * @return string
     */
    public static function addcslashes($str)
    {
        return addcslashes($str);
    }
    
    /**
     * 还原URL 编码字符串
     * @param unknown $str
     * @return string
     */
    public static function urlDecode($str)
    {
        return rawurldecode($str);
    }
    
    /**
     * 将字符串以URL编码
     * @param unknown $str
     * @return string
     */
    public static function urlEncode($str)
    {
        return rawurlencode($str);
    }
    
    /**
     * 将数值转换成json数据存储格式
     * @param unknown $data
     * @return string
     */
    public static function jsonEncode($data)
    {
        return json_encode($data);
    }
    
    /**
     *  对 JSON 格式的字符串进行编码
     * @param unknown $data
     * @return mixed
     */
    public static function jsonDecode($data)
    {
        return json_decode($data, true);
    }
    
    /**
     * 计算字符串长度（特殊符号转义后计算,中文按3字符计算)
     * @param unknown $str
     * @return number
     */
    public static function strLength($str)
    {
        $str = trim(htmlspecialchars($str, ENT_QUOTES));
        
        return mb_strlen($str, 'utf-8');
    }
    
    /**
     * 按字截取字符（支持中文）
     * @param unknown $str
     * @param unknown $start
     * @param unknown $end
     * @return string
     */
    public static function substr($str, $start, $end)
    {
        $str = mb_substr($str, $start, $end, 'utf-8');
        return htmlentities($str, ENT_QUOTES);
    }
    
    /**
     * 把一些预定义的字符转换为 HTML字符串
     * @param unknown $str
     * @return string
     */
    public static function htmlspecialchars($str)
    {
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        return htmlspecialchars($str, ENT_QUOTES);
    }
    
    /**
     * 把一些预定义的 HTML 实体转换为字符
     * @param unknown $str
     * @return string
     */
    public static function htmlspecialchars_decode($str)
    {
        return htmlspecialchars_decode($str, ENT_QUOTES);
    }
    
    /**
     * 删除数组中由 addcslashes() 函数添加的反斜杠
     * @param unknown $data
     * @return string
     */
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
     * 提取数字
     * 
     * @param type $number
     * @return type 
     */
    public static function numeric($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }
    
    /**
     * 深度trim字符串
     * @param mix $params
     */
    public static function deepTrim($params)
    {
    	if(is_array($params))
    	{
    		foreach ($params as $k => $v)
    		{
    			$params[$k] = $this->deepTrim($v);
    		}
    	} else 
    	{
    		return trim($params);
    	}
    	
    	return $params;
    }
}

?>