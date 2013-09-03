<?php

return array(
    'production' => array(
        'phpSettings' => array(
            'display_startup_errors' => 1,
            'display_errors' => 1,
        ),
        'includePaths' => array(
            'library' => APPLICATION_PATH . "/../library",
        ),
        'bootstrap' => array(
            'path' => APPLICATION_PATH . "/Bootstrap.php",
            'class' => 'Bootstrap',

        ),
        'appnamespace' => 'Application',
        'resources' => array(
            'frontController' => array(
                'params' => array(
                    'displayExceptions' => 0,
                ),
                'controllerDirectory' => APPLICATION_PATH . "/controllers",
            ),
            'layout' => array(
                'layoutPath' => APPLICATION_PATH . "/layouts/scripts/",
            ),
            'view' => array(
                'js' => array(
                    'base' => 'http://www.star.com'
                )
            ),
            'db' => array(
                'adapter' => 'Mysqli',
                'params' => array(
                    'host' => '127.0.0.1',
                    'dbname' => 'test',
                    'username' => 'root',
                    'password' => '123456'
                ),
                'multi_slave_db' => false,
                'slave_db' => array(
                    0 => array(
                        'host' => '127.0.0.1',
                        'dbname' => 'test',
                        'username' => 'root',
                        'password' => '123456'
                    ),
                    1 => array(
                        'host' => '127.0.0.1',
                        'dbname' => 'test',
                        'username' => 'root',
                        'password' => '123456'
                    ),
                    2 => array(
                        'host' => '127.0.0.1',
                        'dbname' => 'test',
                        'username' => 'root',
                        'password' => '123456'
                    ),
                    3 => array(
                        'host' => '127.0.0.1',
                        'dbname' => 'test',
                        'username' => 'root',
                        'password' => '123456'
                    ),
                )
            ),
        ),
        'cache' => array(
            'is_cache' => false,
            'type' => 'redis',
            'multi_cache' => true,
            'server' => array(
                0 => array(
                    'host' => '127.0.0.1',
                    'port' => '11211',
                ),
                1 => array(
                    'host' => '127.0.0.1',
                    'port' => '11211',
                ),
                2 => array(
                    'host' => '127.0.0.1',
                    'port' => '11211',
                ),
                3 => array(
                    'host' => '127.0.0.1',
                    'port' => '11211',
                ),

            ),

        ),
    ),
);

?>