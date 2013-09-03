<?php
/**
 *
 * Date类
 *
 * @author zhangqy
 *
 */

class Star_Date {
	
    /**
     * 日期转换为时间戳
     * 
     * @param type $date
     * @param type $is_first
     * @return type 
     */
    public static function dateToTime($date, $is_first = true)
    {
        list($year, $month, $day) = explode('-', $date);

        return $is_first == true ? mktime(0, 0, 0, $month, $day, $year) : mktime(23, 59, 59, (int) $month, (int) $day, (int) $year);
    }
    
    /**
     * 时间戳转换为日期
     * @param type $time
     * @return type 
     */
    public static function timeToDate($time)
    {
        return date('Y-m-d H:i:s', $time);
    }
    
    /**
     * 返回当前第几周
     * 
     * @param type $time
     * @return type 
     */
    public static function getWeek($time)
    {
        return date('W', $time); 
    }
    
    /**
     * 返回年周
     * 
     * @param type $time
     * @return type 
     */
    public static function getYearWeek($time)
    {
        return date('YW', $time);
    }
    
    /**
     * 返回年月
     * 
     * @param type $time
     * @return type 
     */
    public static function getYearMonth($time)
    {
        return date('Ym', $time);
    }

    /**
     * 返回上周起始时间戳
     * 
     * @param type $is_first
     * @return type 
     */
    public static function getLastWeek($is_first = true)
    {
        $now_time = time();
        
        $week = date('w', $now_time); //星期几
        
        list($year, $month, $day) = explode('-', date('Y-m-d', $now_time - ($week + 7 - 1) * 86400));
        
        $time = mktime(0, 0, 0, $month, $day, $year);
        
        if ($is_first == false)
        {
            $time += 7 * 86400 - 1;
        }
       
       return $time;
    }
    
    /**
     * 返回上月起始时间戳
     * 
     * @param type $is_first
     * @return type 
     */
    public static function getLastMonth($is_first = true)
    {
        $now_time = time();
        
        list($year, $month) = explode('-', date('Y-m', ($now_time - ((date('d', $now_time) + 1) * 86400)))); //返回上个月年月

        if ($is_first == true)
        {
            $time = mktime(0, 0, 0, $month, 1, $year);
        } else {
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year); //上个月总共天数
            
            $time = mktime(23, 59, 59, $month, $days, $year);
        }
        
        return $time;
    }
    
    /**
     * 返回昨天起始时间戳
     * 
     * @param type $is_first
     * @return type 
     */
    public static function getLastDay($is_first = true)
    {
        list($year, $month , $day) = explode('-', date('Y-m-d', (time() - 86400)));
        
        if ($is_first == true)
        {
            $time = mktime(0, 0, 0, $month, $day, $year);
        } else {
            $time = mktime(23, 59, 59, $month, $day, $year);
        }
        
        return $time;
    }
    
    /**
     * 返回当月开始时间
     * 
     * @return type 
     */
    public static function getThisMonth()
    {
        list($year, $month, $day) = explode('-', date('Y-m-d', time()));
        
        return mktime(0, 0, 0, $month, 1, $year);
    }
    
    /**
     * 返回当天开始时间
     * 
     * @return type 
     */
    public static function getThisDay()
    {
        list($year, $month, $day) = explode('-', date('Y-m-d', time()));
        
        return mktime(0, 0, 0, $month, $day, $year);
    }
}

?>