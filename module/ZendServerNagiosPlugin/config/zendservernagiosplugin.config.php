<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Clusterstatus' => 'ZendServerNagiosPlugin\Controller\ClusterstatusController',
						'AuditTrail' => 'ZendServerNagiosPlugin\Controller\AudittrailController',
						'Events' => 'ZendServerNagiosPlugin\Controller\EventController',
						'Notifications' => 'ZendServerNagiosPlugin\Controller\NotificationsController',
						'License' => 'ZendServerNagiosPlugin\Controller\LicenseController',
						'JobQueue' => 'ZendServerNagiosPlugin\Controller\JobqueueController',
						//'OptimizerPlus' => 'ZendServerNagiosPlugin\Controller\OptimizerplusController',
						'DaemonsProbe' => 'ZendServerNagiosPlugin\Controller\DaemonsprobeController',
						//'Phpworkers' => 'ZendServerNagiosPlugin\Controller\PhpworkersController',
						'Processingtime' => 'ZendServerNagiosPlugin\Controller\ProcessingtimeController',
						
						'Nagios' => 'ZendServerNagiosPlugin\Controller\NagiosController',
						'Core' => 'ZendServerNagiosPlugin\Controller\CoreController'
				) 
		),
		'service_manager' => array(
			'factories' => array(
					'NagiosCallsBufferManager' => 'ZendServerNagiosPlugin\Service\NagiosCallsBufferManagerFactory',
					'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
					'NagiosTableGateway' => 'ZendServerNagiosPlugin\Service\NagiosTableGatewayFactory',
			),
		),
		'console' => array (
				'router' => array (
						'default_params' => array (
								'controller' => 'ConsoleController' 
						),
						'routes' => array (
								//*********************    Probes    ********************
								//Cluster status
								'nagios-command-clusterstatus' => array (
										'options' => array (
												'route' => 'nagiosplugin clusterstatus',
												'defaults' => array (
														'controller' => 'Clusterstatus',
														'action' => 'clusterstatus' 
												),
												'usage' => 'Get Cluster status' 
										) 
								),
								//AuditTrail
								'nagios-command-audittrail' => array (
										'options' => array (
												'route' => 'nagiosplugin audittrail',
												'defaults' => array (
														'controller' => 'AuditTrail',
														'action' => 'audittrail',
												),
												'usage' => 'Get last audit trails' 
										) 
								),
								//Monitor events 
								'nagios-command-events-cluster' => array (
										'options' => array (
												'route' => 'nagiosplugin events-cluster',
												'defaults' => array (
														'controller' => 'Events',
														'action' => 'eventscluster'
												),
												'usage' => 'get last Monitor events (cluster)'
										)
								),
								'nagios-command-events-node' => array (
										'options' => array (
												'route' => 'nagiosplugin events-node',
												'defaults' => array (
														'controller' => 'Events',
														'action' => 'eventsnode'
												),
												'usage' => 'get last Monitor events (cluster)'
										)
								),
								//Notification
								'nagios-command-notifications' => array (
										'options' => array (
												'route' => 'nagiosplugin notifications',
												'defaults' => array (
														'controller' => 'Notifications',
														'action' => 'notifications' 
												),
												'usage' => 'Get curent notifications' 
										) 
								),
								//License
								'nagios-command-license' => array (
										'options' => array (
												'route' => 'nagiosplugin license',
												'defaults' => array (
														'controller' => 'License',
														'action' => 'license' 
												),
												'usage' => 'Check lisence validity' 
										) 
								),
								//Job Queue
								'nagios-command-jobqueue' => array (
										'options' => array (
												'route' => 'nagiosplugin jobqueue',
												'defaults' => array (
														'controller' => 'JobQueue',
														'action' => 'jobqueue'
												),
												'usage' => 'Check if there are failed jobs'
										)
								),
								//Optimizer + (not implemented)
								/*'nagios-command-optimizerplus' => array (
										'options' => array (
												'route' => 'nagiosplugin optimizerplus',
												'defaults' => array (
														'controller' => 'Optimizerplus',
														'action' => 'optimizerplus'
												),
												'usage' => 'Check Optimizer+ stats'
										)
								),*/
								//Daemons probe
								'nagios-command-daemonsprobe-cluster' => array (
										'options' => array (
												'route' => 'nagiosplugin daemonsprobe-cluster',
												'defaults' => array (
														'controller' => 'DaemonsProbe',
														'action' => 'daemonsprobecluster'
												),
												'usage' => 'Check if some daemons have bad status'
										)
								),
								'nagios-command-daemonsprobe-node' => array (
										'options' => array (
												'route' => 'nagiosplugin daemonsprobe-node',
												'defaults' => array (
														'controller' => 'DaemonsProbe',
														'action' => 'daemonsprobenode'
												),
												'usage' => 'Check if some daemons have bad status'
										)
								),
								//PHP workers (not implented)
								/*'nagios-command-phpworkers-cluster' => array (
										'options' => array (
												'route' => 'nagiosplugin phpworkers-cluster',
												'defaults' => array (
														'controller' => 'Phpworkers',
														'action' => 'Phpworkers'
												),
												'usage' => 'Check number of PHP workers'
										)
								),*/
								//Processing time
								'nagios-command-processingtime-cluster' => array (
										'options' => array (
												'route' => 'nagiosplugin processingtime-cluster',
												'defaults' => array (
														'controller' => 'Processingtime',
														'action' => 'processingtime'
												),
												'usage' => 'Check average request processing time'
										)
								),
								
								
								//********************    Utils     ********************
								//Nagios utils (use it with root credentials)
								'add-nrpe-command' => array(
										'options' => array (
												'route' => 'nagiosplugin add-nrpe-command --commandId= --command=',
												'defaults' => array (
														'controller' => 'Nagios',
														'action' => 'addnrpecommand'
												),
												'usage' => 'Add a command to the nrpe-server (use it with root credentials)'
										)
								),
								'restart-nrpe-server' => array(
										'options' => array (
												'route' => 'nagiosplugin restart-nrpe-server',
												'defaults' => array (
														'controller' => 'Nagios',
														'action' => 'restartnrpeserver'
												),
												'usage' => 'Restart nrpe server (use it with root credentials)'
										)
								),
								
								//Install node
								'install-node' => array(
										'options' => array (
												'route' => 'nagiosplugin install-node',
												'defaults' => array (
														'controller' => 'Core',
														'action' => 'installnode'
												),
												'usage' => 'Install plugin on the current node (use it with root credentials)'
										)
								),
								
								
						) 
				) 
		) 
);
