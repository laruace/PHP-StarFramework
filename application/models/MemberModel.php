<?php

class MemberModel extends Star_Model_Api {
    
    protected $server_name = 'www.baidu.com';

    public function get()
    {
        return $this->api('', '');
    }
    //put your code here
}

?>
