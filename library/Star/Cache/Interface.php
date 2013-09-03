<?php

interface Star_Cache_Interface {
	
	public function add($key, $value, $lefttime = 0);
	
	public function get($key);
	
	public function set($key, $value, $lefttime = 0);
	
	public function delete($key);
}

?>