<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class AudittrailController extends AbstractNagiosController 
{
    /**
     * Check for the last audi trail event on the current node
     * Nagios severity level depends on the audit type.
     */
    public function audittrailAction ()
    {
    	$auditTrail = $this->sendApiMethod('auditGetList',
    			array(
    					'order' => 'creation_time',
    					'direction' => 'ASC',
    					'filters' => array(
    							'from' => $this->getLastTouch()->getLastTouch(),
    							'to' => time()
    					)
    			)
    	);
    	$statusMessage = '';
    	$severity = self::NAGIOS_OK;
    	$threshold = $this->getNagiosThresholdConfig();
    	foreach ($auditTrail->responseData->auditMessages->auditMessage as $auditMessage) {
    		$creationTime = (string) $auditMessage->creationTime;
    		$user = (string) $auditMessage->username;
    		$interface = (string) $auditMessage->requestInterface;
    		$type = (string) $auditMessage->auditType;
    		$audittime = strtotime($creationTime);
    		$statusMessage .= $user . " - ";
    		$statusMessage .= $interface . " - ";
    		$statusMessage .= $type . " - ";
    		$statusMessage .= $creationTime;
    		$statusMessage .= "\n";
    		$nagiosSeverity = self::NAGIOS_OK;
    		if (isset($threshold[$type])) {
    			$nagiosSeverity = constant('self::' . $threshold[$type]);
    		}
    		if ($nagiosSeverity > $severity)
    			$severity = $nagiosSeverity;
    	}
    	if ($statusMessage == '') $statusMessage = 'No audit trail item';
    	$this->setStatusMessage($statusMessage);
    	$this->setStatus($severity);
    }
}