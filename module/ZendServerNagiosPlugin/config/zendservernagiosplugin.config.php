<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'ClusterController' => 'ZendServerNagiosPlugin\Controller\ClusterController',
						'FrontendController' => 'ZendServerNagiosPlugin\Controller\FrontendController' 
				) 
		),
		'service_manager' => array(
			'factories' => array(
					'NagiosCallsBufferManager' => 'ZendServerNagiosPlugin\Service\NagiosCallsBufferManagerFactory'
			),
		),
		'console' => array (
				'router' => array (
						'default_params' => array (
								'controller' => 'ConsoleController' 
						),
						'routes' => array (
								//Cluster status (cluster wide)
								'clusterstatus' => array (
										'options' => array (
												'route' => 'nagiosplugin clusterstatus',
												'defaults' => array (
														'controller' => 'ClusterController',
														'action' => 'clusterstatus' 
												),
												'usage' => 'Get Cluster status' 
										) 
								),
								//AuditTrail (Cluster wide)
								'audittrail' => array (
										'options' => array (
												'route' => 'nagiosplugin audittrail',
												'defaults' => array (
														'controller' => 'ClusterController',
														'action' => 'audittrail',
												),
												'usage' => 'Get last audit trails' 
										) 
								),
								'notifications' => array (
										'options' => array (
												'route' => 'nagiosplugin notifications',
												'defaults' => array (
														'action' => 'notifications' 
												),
												'usage' => 'Get curent notifications' 
										) 
								),
								'license' => array (
										'options' => array (
												'route' => 'nagiosplugin license',
												'defaults' => array (
														'action' => 'license' 
												),
												'usage' => 'Check lisence validity' 
										) 
								),
								'events' => array (
										'options' => array (
												'route' => 'nagiosplugin events [--delay=] [--limit=]',
												'defaults' => array (
														'action' => 'events' 
												),
												'usage' => 'get last Monitor events' 
										) 
								),
								'install' => array (
										'options' => array (
												'route' => 'nagiosplugin install --nagiosDir=',
												'defaults' => array (
														'action' => 'install',
														'controller' => 'FrontendController' 
												),
												'usage' => 'Install the plugin (use it as root)' 
										) 
								) 
						) 
				) 
		) 
);
