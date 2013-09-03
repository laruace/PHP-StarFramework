<?php
/**
 *
 * Log操作类
 *
 * @author zhangqy
 *
 */

class Star_Log {
	
    protected static $directory_name = 'logs';
    
    private static $model = 'a+';


    public function __construct() {
        
    }
    
    /**
     * 写日志
     * 
     * @param type $message 
     */
    public static function log($message, $type = 'error')
    {
        $now = time();
        
        $time = date('Y-m-d H:i:s', $now);
        
        $file_name = "{$type}_" . date('Ymd', $now);
        
        $message = "\n{$time}:  {$message}";
        
        $directory = Star_Loader::getModuleDirect(self::$directory_name);
        
        if (!is_dir($directory))
        {
            mkdir($directory, 0775, true);
        }
        
        $file_path = Star_Loader::getFilePath(array($directory,  $file_name), '.txt');

        $handle = fopen($file_path, self::$model, false);
        
        fwrite($handle, $message);
        
        fclose($handle);
    }
    
    /**
     * 设置log文件目录
     * 
     * @param type $directory_name 
     */
    public static function setLogDirectory($directory_name)
    {
        self::$log_directory = $directory_name;
    }
}

?>