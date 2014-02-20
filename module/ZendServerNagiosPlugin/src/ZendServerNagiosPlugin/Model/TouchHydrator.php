<?php
namespace ZendServerNagiosPlugin\Model;

use Zend\Stdlib\Hydrator\HydratorInterface;

class TouchHydrator implements HydratorInterface
{
	/**
	* Extract values from an object
	*
	* @param  object $object
	* @return array
	*/
	public function extract($object)
	{
		return array(
			'service' => $object->getCommand(),
			'node_id' => $object->getNodeId(),
			'footprint' => $object->getFootprint(),
			'last_touch' => $object->getLastTouch(),
		);
	}
	
	/**
	 * Hydrate $object with the provided $data.
	 *
	 * @param  array $data
	 * @param  object $object
	 * @return object
	*/
	public function hydrate(array $data, $object)
	{
		$object->setCommand($data['service']);
		$object->setNodeId($data['node_id']);
		$object->setLastTouch($data['last_touch']);
		$object->setFootprint($data['footprint']);
		return $object;
	}
}