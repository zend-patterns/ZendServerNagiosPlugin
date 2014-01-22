<?php
$deploymentToolsPath = zend_deployment_library_path('ZendserverDeploymentTools');
require_once $deploymentToolsPath . '/init_autoloading.php';
require_once 'Deployer.php';

$deployer = new Deployer('deployment.config.php');
$deployer->run();