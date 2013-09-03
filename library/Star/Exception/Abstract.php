<?php

/**
 *
 *
 * @author zhangqy
 *
 */
abstract class Star_Exception_Abstract extends Exception {
	
	public function __construct($message, $code = 0, $previous = NULL)
	{
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            parent::__construct($message, (int) $code);
            $this->_previous = $previous;
        } else {
            parent::__construct($message, (int) $code, $previous);
        }
	}
	
	public function __call($method, $args)
	{
		return null;
	}
    
    public function __toString()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            if (null !== ($e = $this->getPrevious())) {
                return $e->__toString()
                       . "\n\nNext "
                       . parent::__toString();
            }
        }
        return parent::__toString();
    }
    
    protected function _getPrevious()
    {
        return $this->_previous;
    }

}

?>