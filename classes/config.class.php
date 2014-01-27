<?php

class config
{
	static public $config=array();
	
	
	private function __construct()
	{
		
	}
	static public function setup(){
		config::set('ta_externalLogin', '');
		config::set('ta_password', '');
		config::set('ta_replyemail', '');
		error_reporting(E_ALL ^ E_NOTICE);
		
                
                
                config::set('output_to_screen',true);


                config::set('email_reports',array());


                

                config::set('lost_bag_number', '');
				config::set('customer_services', '');
				
                config::set('DATE_FORMAT', 'Y-m-d');

                define('ORIGINATOR', '');//for users to include in sms messages

                // HTTP
                define('HTTP_SERVER', '');
                define('HTTP_IMAGE', '');
                define('HTTP_ADMIN', '');

                // HTTPS
                define('HTTPS_SERVER', '');
                define('HTTPS_IMAGE', '');

                // DIR
                define('DIR_APPLICATION', '');
                define('DIR_SYSTEM', '');
                define('DIR_DATABASE', '');
                define('DIR_LANGUAGE', '');
                define('DIR_TEMPLATE', '');
                define('DIR_CONFIG', '');
                define('DIR_IMAGE', '');
                define('DIR_CACHE', '');
                define('DIR_DOWNLOAD', '');
                define('DIR_LOGS', '');

                // DB                
                config::set('mysql_conn','localhost:USER:PASSWORD:SCHEMA');
                
                //email
               
                config::set('sita_imap_host', '{imap.gmail.com:993/imap/ssl}INBOX');
                config::set('sita_imap_email_address', '');
                config::set('sita_imap_password', '');
        
                config::set('config_mail_protocol', 'mail');
                config::set('config_mail_parameter', '');
                config::set('config_smtp_host', 'smtp.gmail.com');
                config::set('config_smtp_username', '');
                config::set('config_smtp_password', '');
                config::set('config_smtp_port', '465');
                config::set('config_smtp_timeout', '2');
                	
                
                
	}
	static public function set($name,$value)
	{
		self::$config[$name]=$value;
	}
	
	static public function get($name)
	{
		return self::$config[$name];
	}
	
	static public function dump()
	{
		print_r(self::$config);
	}
}
?>
