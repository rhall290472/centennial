<?php
	
	
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    //ip from share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //ip pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$userdata  = array();

if (!strcmp($ip, "::1")) {// DB Connection Configuration
	define('DB_HOST', 'localhost'); 
	define('DB_USERNAME', 'webuser'); 
	define('DB_PASSWORD', 'webuser'); 
	define('DATABASE', 'esc'); 
	define('TABLE', 'events');
	define('USERS_TABLE', 'users');
}
else{
	define('DB_HOST', 'rhall29047217205.ipagemysql.com'); 
	define('DB_USERNAME', 'gccescusr'); 
	define('DB_PASSWORD', '_w3frRWX^&q'); 
	define('DATABASE', 'esc'); 
	define('TABLE', 'events');
	define('USERS_TABLE', 'users');

}
	
	define('SITE_FILES_URL', '');
	
	// Default Categories
	$categories = array("ESC", "Training");
	
	/*
	Only applied for non user versions
	Should (non admin versions) display user events from the database?
	 true - does not display user events 
	 false - will display all events on the database even private ones on non admin versions (e.g: 'Simple')
	*/
	define('PUBLIC_PRIVATE_EVENTS', true);
	
	// Feature to import events
	define('IMPORT_EVENTS', true);
	
?>