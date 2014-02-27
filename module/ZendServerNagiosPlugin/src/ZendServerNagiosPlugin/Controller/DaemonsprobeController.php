<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class DaemonsprobeController extends AbstractNagiosController 
{
    /**
     * Check for daemons status (cluster wide)
     */
    public function daemonsprobeclusterAction ()
    {
    	$this->setRequestingModeCluster();
        $daemonsprobe = $this->sendApiMethod('daemonsProbe');
        $threshold = $this->getNagiosThresholdConfig('daemonsprobe');
        $severity = self::NAGIOS_OK;
        $statusMessage = '';
        if ($this->hasChange()) {
	        foreach ($daemonsprobe->responseData->daemonMessages->daemonMessage as $message) {
	            $statusMessage .= str_replace('"', '', (string)$message->key) ;
	            $statusMessage .= ' (' . (string)$message->nodeId . ') : ';
	            $statusMessage .= str_replace('"', '', (string)$message->details) . ' | ';
	            if ($threshold[(string)$message->severity]) {
	            	$tmpSeverity = constant('self::' . $threshold[(string)$message->severity]);
	            	if ($tmpSeverity > $severity) $severity = $tmpSeverity;
	            }
	        }
        }
        if ($statusMessage == '') $statusMessage = 'no alert';
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
    
    /**
     * Check for daemons status (node)
     */
    public function daemonsprobenodeAction ()
    {
    	$this->setRequestingModeNode();
    	$daemonsprobe = $this->sendApiMethod('daemonsProbe');
    	$threshold = $this->getNagiosThresholdConfig('daemonsprobe');
    	$severity = self::NAGIOS_OK;
    	//$serverId = $this->getNodeId();
    	$statusMessage = '';
    	if ($this->hasChange()) {
	    	foreach ($daemonsprobe->responseData->daemonMessages->daemonMessage as $message) {
	    		$nodeId = (string)$message->nodeId;
	    		if ($nodeId != $serverId) continue;
	    		$statusMessage .= str_replace('"', '', (string) $message->key) . ' : ';
	    		$statusMessage .= str_replace('"', '', (string) $message->details) . ' | ';
	    		if ($threshold[(string)$message->severity]) {
	    			$tmpSeverity = constant('self::' . $threshold[(string)$message->severity]);
	    			if ($tmpSeverity > $severity) $severity = $tmpSeverity;
	    		}
	    	}
    	}
    	if ($statusMessage == '') $statusMessage = 'no alert';
    	$this->setStatusMessage($statusMessage);
    	$this->setStatus($severity);
    }
}