<?php
return array(
	'zsapi' => array (
		'target' => array(
			'zsurl' => 'http://localhost:10081/ZendServer',
			'zskey' => 'admin',
			'zssecret' => 'fdbe02cc14bf1b48787e379bc420fd46ba9c88a3cc85c2030b5e32610e75efa5',
			'zsversion' => '6.2'
		),
	),
	'nagios' => array(
		'plugin' => array(
			'directory' => '/var/www/ZendServerNagiosPlugins'
		),
		'client' => array(
				'config' => array(
						'directory' => '/etc/nagios'
				),
		),
	),
);