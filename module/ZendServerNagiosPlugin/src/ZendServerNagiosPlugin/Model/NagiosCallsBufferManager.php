<?php
namespace ZendServerNagiosPlugin\Model;

class NagiosCallsBufferManager
{
	/**
	 * Called when Nagios ask infromation to a node
	 * 
	 * @param string $command
	 */
	public function nodeTouch($command)
	{
		file_put_contents(__ROOT__ .'/data/callsbuffer/node/' . $command, time());
	}
	
	/**
	 * Get thje last cmmand call on the current node
	 * 
	 * @param string $command
	 * @return mixed
	 */
	public function getLastNodeTouch($command)
	{
		$time = file_get_contents(__ROOT__ . '/data/callsbuffer/node/' . $command);
		return $time;
	}
}