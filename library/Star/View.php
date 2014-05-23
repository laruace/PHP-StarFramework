<?php
/**
 * @package library\Star
 */

/**
 * 导入文件
 */
require 'Star/View/Abstract.php';

/**
 * Start_view
 *
 * @package library\Star
 * @author zhangqy
 *
 */
class Star_View extends Star_View_Abstract{
	
	/**
	 * 构造方法
	 * @param string $application_path
	 * @param unknown $options
	 */
	public function __construct($application_path = '', $options = array())
	{
		parent::__construct($application_path, $options);
	}
	
	/**
	 * 
	 * @see Star_View_Abstract::run()
	 */
	protected function run()
	{
        if ($this->getBasePath() == null)
        {
            $base_path = Star_Loader::getDirPath(array($this->application_path, $this->default_view));

            $this->setBasePath($base_path);
        }
	}
	
    /**
     * 设置配置项
     * 
     * @param array $options 
     */
    public function setOption(array $options) 
    {
        if (!empty($options))
        {
            $methods = get_class_methods($this);

            foreach ($options as $method => $option)
            {
                $method = "set" . ucfirst($method);

                if (in_array($method, $methods))
                {
                    call_user_func(array($this, $method), $option);
                }
            }
        }

    }
}

?>