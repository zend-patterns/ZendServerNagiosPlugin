<?php
require 'ZendDevops/init_autoloading.php';
require_once 'NagiosPluginDeployer.php';

$depH = new NagiosPluginDeployer('nagios.deployment.config.php');
$depH->run();
