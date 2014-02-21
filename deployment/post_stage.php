<?php
$configArray = array(
	'target_manager_config' => array (
			'default' => array(
					'zsurl' => getenv('ZS_SERVER_URI'),
					'zskey' => getenv('ZS_WEB_API_KEY_NAME'),
					'zssecret' => getenv('ZS_WEB_API_KEY_SECRET'),
					'zsversion' => getenv('ZS_ZS_VERSION')
			),
	),
	'nagios' => array(
			'plugin' => array(
					'directory' => getenv('ZS_APPLICATION_BASE_DIR'),
			),
			'client' => array(
					'config' => array(
							'directory' => getenv('ZS_NAGIOS_DIR'),
							'service-remote' => getenv('ZS_NAGIOS_SERVICE')
					),
			),
	),
);

$arrayStr = '<?php return ' . var_export($configArray, true) .';';
file_put_contents(getenv('ZS_APPLICATION_BASE_DIR') .'/config/autoload/config.local.php', $arrayStr);