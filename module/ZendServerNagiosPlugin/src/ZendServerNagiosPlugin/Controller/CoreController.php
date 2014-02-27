<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\AbstractActionController;
use ZendServerNagiosPlugin\Model\Nagios\NrpeCfg;
use Zend\Config\Reader\Ini;
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;

class CoreController extends AbstractNagiosController
{
	/**
	 * Add a command in the nrep.cfg file.
	 */
	public function installnodeAction()
	{
		$this->setFrontScriptExecutable();
		$this->installDatabaseConfig();
		$this->configureNrpeServer();
		$this->forward()->dispatch('Nagios', array('action' => 'restartnrpeserver'));
		exec ('/usr/local/zend/bin/php ' . __ROOT__ . '/index.php nagiosplugin install-database');
		exec ('/usr/local/zend/bin/php ' . __ROOT__ . '/index.php nagiosplugin install-finish');
	}
	
	/**
	 * The the index.php as an executable;chmod 755
	 */
	public function setFrontScriptExecutable()
	{
		exec ('chmod 755 ' . __ROOT__ . '/index.php');
	}
	
	/**
	 * Get Nagios client plugin configuration
	 * @return array
	 */
	protected function getNagiosConfig()
	{
	    $config =  $this->getServiceLocator()->get('config');
	    return $config['nagios'];
	}
	
	/**
	 * Add the Zend Server database config to the main local config.
	 */
	protected function installDatabaseConfig()
	{
		$configReader  = new Ini();
		$zsDbConfig = $configReader->fromFile('/usr/local/zend/etc/zend_database.ini');
		$dsn  = 'mysql:dbname=' . $zsDbConfig['zend']['database']['name'] . ';';
		$host = $zsDbConfig['zend']['database']['host_name'];
		if ($host == 'localhost') $host = '127.0.0.1';
		$dsn .= 'host=' . $host;
		$dbConfig = new Config(array(
				'db' => array(
						'driver'   => 'Pdo',
						'dsn' => $dsn,
						'username' => $zsDbConfig['zend']['database']['user'],
						'password' => $zsDbConfig['zend']['database']['password'],
				),
		));
		$dbConfigWritter = new PhpArray();
		$dbConfigWritter->toFile(__ROOT__ . '/config/autoload/zsdatabase.local.php', $dbConfig);
		
	}
	
	/**
	 * Configure nrpe server
	 * 
	 * Add plugin command to nrpe server configuration
	 */
	protected function configureNrpeServer()
	{
		$nagiosConfig = $this->getNagiosConfig();
		$configFilePath = $nagiosConfig['client']['config']['directory'] . '/nrpe.cfg';
		$nrpeConfig = new NrpeCfg($configFilePath);
		$nrpeConfig->removeZsCommands();
		$config = $this->getServiceLocator()->get('config');
		$commandRoutes = $config['console']['router']['routes'];
		foreach ($commandRoutes as $commandId => $routeConfig){
			if (substr($commandId,0,15) != 'nagios-command-') continue;
			$commandId = str_replace('nagios-command-', '', $commandId);
			$command = __ROOT__ . '/index.php ' . $routeConfig['options']['route'];;
			$nrpeConfig->addCommand($commandId , $command);
		}
		$nrpeConfig->toFile();
	}
	
	/**
	 * Install Nagios table in Zend Server database
	 */
	public function installdatabaseAction()
	{
	    $config = $this->getServiceLocator()->get('config');
		$adapter = new \Zend\Db\Adapter\Adapter($config['db']);
		$tmp = $adapter->query('SHOW TABLES LIKE "nagios";')->execute();
		$isInstalled = $tmp->current();
		if ($isInstalled) return;
		$queryString = file_get_contents(__DIR__ . '/../../../config/schema.sql');
		$result = $adapter->query($queryString)->execute();
	}
	
	/**
	 * Finish installation process
	 */
	public function installfinishAction()
	{
	    //Set node id
	    $clusterStatus = $this->sendApiMethod('clusterGetServerStatus');
	    if ( ! $clusterStatus) return false;
	    $currentHost = gethostname();
	    foreach ($clusterStatus->responseData->serversList->serverInfo as $serverInfo){
	    	$serverId = (string)$serverInfo->id;
	    	$name = (string)$serverInfo->name;
	    	if ($currentHost == $name) {
	    		$nodeId = $serverId;
	    	}
	    }
	    $configArray = include __ROOT__ . '/config/autoload/config.local.php';
	    $configArray['nodeId'] = $nodeId;
	    $arrayStr = '<?php return ' . var_export($configArray, true) .';';
	    file_put_contents(__ROOT__ .'/config/autoload/config.local.php', $arrayStr);
	}
	
}