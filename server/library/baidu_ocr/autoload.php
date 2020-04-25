<?php

spl_autoload_register(function ($class_name) {
	if (file_exists(__DIR__."/CLASS/". $class_name . '.php')) {
 	   require_once __DIR__."/CLASS/". $class_name . '.php';
	}else{
		return false;
	}
});
