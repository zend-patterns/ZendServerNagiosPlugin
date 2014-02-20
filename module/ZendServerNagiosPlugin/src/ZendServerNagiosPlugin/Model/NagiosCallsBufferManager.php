<?php
/**
 * Record touches make on nodes and cluster by Nagios service
 * 
 * Use "nagios" table in zend server database.
 * @author sophie
 *
 */
namespace ZendServerNagiosPlugin\Model;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Db\RowGateway\RowGateway;
use ZendServerNagiosPlugin\Model\Touch;
use ZendServerNagiosPlugin\Model\TouchHydrator;

class NagiosCallsBufferManager implements ServiceManagerAwareInterface
{
	/**
	 * Service Manager
	 * 
	 * @var ServiceManager
	 */
	protected $serviceManager;
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\ServiceManagerAwareInterface::setServiceManager()
	 */
	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * Get the given touch
	 * 
	 * @param string $command
	 * @param int $nodeId
	 * @return \Zend\Db\RowGateway\RowGateway
	 */
	protected function getTouch($command,$nodeId)
	{
		$nagiosTable = $this->serviceManager->get('NagiosTableGateway');
		$dbAdapter = $nagiosTable->getAdapter();
		$touch = $nagiosTable->select(array('node_id' => $nodeId, 'service' => $command));
		if (count($touch) != 0) {
			$touch = $touch->current();
		}
		else {
			$touch = Touch::factory($command,$nodeId);
		}
		return $touch;
	}
	
	/**
	 * Called when Nagios ask information to a node
	 * 
	 * @param string $command
	 * @param int $nodeId 
	 */
	public function nodeTouch($command, $nodeId, $footprint = null)
	{
		$touch = $this->getTouch($command,$nodeId);
		if ($footprint) $touch->setFootprint($footprint);
		$nagiosTable = $this->serviceManager->get('NagiosTableGateway');
		$hydrator = new TouchHydrator();
		if ($touch->getLastTouch() == 0 ){
			$touch->setLastTouch(time());
			$data = $hydrator->extract($touch);
			$return = $nagiosTable->insert($data);
		}
		else {
			$touch->setLastTouch(time());
			$data = $hydrator->extract($touch);
			$nagiosTable->update($data, array(
				'node_id' => $touch->getNodeId(),
				'service' => $touch->getCommand(),
			));
		}
	}
	
	/**
	 * Get the last command call on the given node
	 * 
	 * @param string $command
	 * @param int $nodeId
	 * @return mixed
	 */
	public function getLastNodeTouch($command, $nodeId)
	{
		$touch = $this->getTouch($command,$nodeId);
		return $touch;
	}
	
	/**
	 * return the foot print
	 * @param unknown $commande
	 * @param unknown $nodeid
	 */
	public function getNodeFootprint($commande, $nodeid)
	{
		$touch = $this->getTouch($command,$nodeId);
		return $touch->footprint;
	}
	
	/**
	 * Called when Nagios ask information to cluster
	 *
	 * @param string $command
	 */
	public function clusterTouch($command, $footprint = null)
	{
		$this->nodeTouch($command,0, $footprint);
	}
	
	/**
	 * Get the last command call on cluster
	 *
	 * @param string $command
	 * @return int (timestamp)
	 */
	public function getLastClusterTouch($command)
	{
		return $this->getLastNodeTouch($command,0);
	}
}