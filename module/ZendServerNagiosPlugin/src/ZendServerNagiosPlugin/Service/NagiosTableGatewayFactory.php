<?php
namespace ZendServerNagiosPlugin\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use ZendServerNagiosPlugin\Model\Touch;
use ZendServerNagiosPlugin\Model\TouchHydrator;

class NagiosTableGatewayFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
		$resultSet  = new HydratingResultSet(new TouchHydrator(),new Touch());
		$tableGateway = new TableGateway('nagios',$dbAdapter,null, $resultSet);
		return $tableGateway;
	}
	
}