<?php
namespace ZendServerNagiosPlugin\Model;

class Touch
{
	/**
	 * Last time touch (timestamp)
	 * 
	 * @var int
	 */
	protected $lastTouch = 0;
	
	/**
	 * Touch foot print
	 * 
	 * @var string
	 */
	protected $footprint = null;
	
	/**
	 * Nagios command name
	 * 
	 * @var string
	 */
	protected $command = '';
	
	/**
	 * Cluster node Id (0, is the cluster)
	 * 
	 * @var int
	 */
	protected $nodeId = null;
	
	/**
	 * @return the $last_touch
	 */
	public function getLastTouch() {
		return $this->lastTouch;
	}

	/**
	 * @return the $footprint
	 */
	public function getFootprint() {
		return $this->footprint;
	}

	/**
	 * @return the $command
	 */
	public function getCommand() {
		return $this->command;
	}

	/**
	 * @return the $nodeId
	 */
	public function getNodeId() {
		return $this->nodeId;
	}

	/**
	 * @param number $last_touch
	 */
	public function setLastTouch($lastTouch) {
		$this->lastTouch = $lastTouch;
	}

	/**
	 * @param string $footprint
	 */
	public function setFootprint($footprint) {
		$this->footprint = $footprint;
	}

	/**
	 * @param string $command
	 */
	public function setCommand($command) {
		$this->command = $command;
	}

	/**
	 * @param int $nodeId
	 */
	public function setNodeId($nodeId) {
		$this->nodeId = $nodeId;
	}

	/**
	 * Constructor
	 */
	public function __construcct()
	{
		$this->footprint = md5('');
		$this->lastTouch = 0;
	}
	
	/**
	 * Touch factory
	 */
	static public function factory($command,$nodeId)
	{
		$touch = new self();
		$touch->setCommand($command);
		$touch->setNodeId($nodeId);
		$touch->setLastTouch(0);
		return $touch;
	}
	
	/**
	 * Compute a footprint
	 * 
	 * @param string $string
	 * @return string
	 */
	static public function computeFootprint($string)
	{
		return md5($string);
	}
	
	
}