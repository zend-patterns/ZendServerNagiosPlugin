<?php
return array(
	'target_manager_config' => array (
		'default' => array(
			'zsurl' => 'http://localhost:10081/ZendServer',
			'zskey' => 'nagios',
			'zssecret' => 'bbf577555266b24542748d05b892e236d8ee9df0bf864fd9a1a126e5bdaf5d07',
			'zsversion' => '6.3'
		),
	),
    'api_http_client' => array(
    		'class' => 'ZendServerWebApi\Model\Http\Client',
    		'config' => array(
    				'adapter'=> 'ZendServerWebApi\Model\Http\Adapter\Socket'
    		),
    ),
	'nagios' => array(
		'plugin' => array(
			'directory' => '/var/www/ZendServerNagiosPlugins'
		),
		'client' => array(
				'config' => array(
						'directory' => '/etc/nagios',
						'service-remote' => 'service nagios-nrpe-server'
				),
		),
	),
);