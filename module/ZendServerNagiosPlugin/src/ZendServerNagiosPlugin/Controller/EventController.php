<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class EventController extends AbstractNagiosController 
{
    /**
     * Check within the given interval time if any events trail have occurred.
     * Nagios severity level depends on the events severity type.
     */
    public function eventsclusterAction ()
    {
        $events = $this->sendApiMethod('monitorGetIssuesByPredefinedFilter', 
                array(
                        'filterId' => 'All',
                        'direction' => 'DESC',
                		'filters' => array(
    							'from' => $this->getLastTouch()->getLastTouch(),
    							'to' => time()
    					)
                ));
        $statusMessage = '';
        $severity = self::NAGIOS_OK;
        $threshold = $this->getNagiosThresholdConfig('events');
        foreach ($events->responseData->issues->issue as $issue) {
            $issueSeverity = constant('self::' . $threshold[(string) $issue->severity]);
            if ($issueSeverity > $severity) $severity = $issueSeverity;
            $statusMessage .= $this->getNagiosMessage($issue);
        }
        if ($statusMessage == '' ) $statusMessage = 'No event';
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
    
    /**
     * Check within the given interval time if any events trail have occurred. (node based)
     * Nagios severity level depends on the events severity type.
     */
    public function eventsnodeAction ()
    {
    	$this->setRequestingModeNode();
    	$events = $this->sendApiMethod('monitorGetIssuesByPredefinedFilter',
    			array(
    					'filterId' => 'All',
    					'direction' => 'DESC',
    					'filters' => array(
    							'from' => $this->getLastTouch()->getLastTouch(),
    							'to' => time()
    					)
    			));
    	$statusMessage = '';
    	$severity = self::NAGIOS_OK;
    	$threshold = $this->getNagiosThresholdConfig('events');
    	foreach ($events->responseData->issues->issue as $issue) {
    		$serverId = (string) $issue->whatHappenedDetails->serverId;
    		if ($serverId != $this->getNodeId()) continue;
    		$issueSeverity = constant('self::' . $threshold[(string) $issue->severity]);
    		if ($issueSeverity > $severity) $severity = $issueSeverity;
    		$statusMessage .= $this->getNagiosMessage($issue);
    	}
    	if ($statusMessage == '' ) $statusMessage = 'No event';
    	$this->setStatusMessage($statusMessage);
    	$this->setStatus($severity);
    }
    
    /**
     * Create the Nagios message to be displayed
     * @param unknown $xmlIssue
     * @return string
     */
    protected function getNagiosMessage($xmlIssue)
    {
    	$lastOccurance = (string) $xmlIssue->lastOccurance;
    	$rule = (string) $xmlIssue->rule;
    	$statusMessage = $rule . " - ";
    	$statusMessage .= $lastOccurance;
    	$issueId = (string) $xmlIssue->id;
    	$url = $this->getZendServerUrl() . '/Issue?issueId=' . $issueId;
    	$statusMessage .= ' Isssue URL : ' . $url . '        ';
    	$statusMessage .= "\r";
    	return $statusMessage;
    }
    
}