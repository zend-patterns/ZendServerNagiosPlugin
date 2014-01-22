<?php
use Zend\EventManager\Event;

class Deployer extends ZendPattern\ZSDeployment\Deployment\DeploymentListener
{
	public function postStageAction(Event $event)
	{
		$appDir = $this->getContext()->getApplicationBaseDir();
		//Set nagios plugin zs api configuration
		$configArray = array(
			'zsapi' => array(
				'target' => array(
					'zskey' => $this->getContext()->getParam('web_api_key_name'),
					'zssecret' => $this->getContext()->getParam('web_api_key_secret'),
					'zsversion' => $this->getContext()->getWebserverVersion()
				),
			),
			'nagios' => array(
				'plugin' => array('directory' => $appDir),
				'client' => array(
					'config' => array('directory' => $this->getContext()->getParam('nagios_dir')),
				),	
			),
		);
		$configWriter = new Zend\Config\Writer\Ini();
		$filename = $this->getContext()->getApplicationBaseDir() . '/config/config.ini';
		$configWriter->toFile($filename, $configArray);
		$this->getLog()->notice($configArray);
		//Set index.php as executable
		exec('sudo chmod +x ' . $appDir . '/index.php');
		//Setting up Nagios NRPE plugin
		exec('sudo php ' . $appDir. '/index.php nagiosplugin install --nagiosDir="' . $this->getContext()->getParam('nagios_dir'). '"');
		return true;
	}
}