<?php
namespace ZendServerNagiosPlugin\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NagiosCallsBufferManagerFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$manager = new \ZendServerNagiosPlugin\Model\NagiosCallsBufferManager();
		return $manager;
	}
	
}