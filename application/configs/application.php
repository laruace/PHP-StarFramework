<?php

return array(
    'production' => array(
        
        'bootstrap' => array(
         //   'path' => APPLICATION_PATH . "/Bootstrap.php",
            'class' => 'Bootstrap',

        ),
        'resources' => array(
            'frontController' => array(
                'params' => array(
                    'displayExceptions' => 0,
                ),
                'controllerDirectory' => APPLICATION_PATH . "/controllers",
            ),
            

        ),
    ),
);

?>