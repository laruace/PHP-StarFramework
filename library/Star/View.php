<?php
/**
 * Start_view
 *
 * @author zhangqy
 *
 */

require 'Star/View/Abstract.php';

class Star_View extends Star_View_Abstract{
	
	
	public function __construct($application_path = '', $options = array())
	{
		parent::__construct($application_path, $options);
	}
	
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