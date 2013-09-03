<?php
/**
 * Start_view
 *
 * @author zhangqy
 *
 */

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
	
}

?>