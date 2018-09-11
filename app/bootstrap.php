<?php
    //Load Config
    require_once 'config/config.php';

    //load helpers
    require_once 'helpers/url_helper.php';

    //load session helper
    require_once 'helpers/session_helper.php';

    //Auto Load Core Libraries 

    spl_autoload_register(function($className){
        require_once 'libraries/' . $className . '.php';
    });