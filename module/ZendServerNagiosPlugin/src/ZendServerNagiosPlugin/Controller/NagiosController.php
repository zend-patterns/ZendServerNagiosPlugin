<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\AbstractActionController;
use ZendServerNagiosPlugin\Model\Nagios\NrpeCfg;

class NagiosController extends AbstractActionController
{
	/**
	 * Add a command in the nrep.cfg file.
	 */
	public function addnrpecommandAction()
	{
		$nagiosConfig = $this->getNagiosConfig();
		$configFilePath = $nagiosConfig['client']['config']['directory'] . '/nrpe.cfg';
		$commandString =  __ROOT__ . '/index.php nagiosplugin ' . $this->params('command');
		$nrpeConfig = new NrpeCfg($configFilePath);
		$nrpeConfig->addCommand($this->params('commandId') , $commandString);
		$nrpeConfig->toFile();
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
	 * Restart nrpe-server
	 */
	public function restartnrpeserverAction()
	{
		$nagiosConfig = $this->getNagiosConfig();
		$remote = trim($nagiosConfig['client']['config']['service-remote']);
		exec($remote . ' restart', $return);
		$console = $this->getServiceLocator()->get('console');
		foreach ($return as $line){
			$console->write($line. "\n");
		}
	}
}