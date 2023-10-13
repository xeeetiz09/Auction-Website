<?php
// php's built in function to autoload the class (magic function)...
spl_autoload_register(function($class){
    require_once($class.'.php');
});