<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to cluster-wide probes
 */

class ClusterController extends AbstractNagiosController 
{
    /**
     * Check how many nodes are up in the cluster
     */
    public function clusterstatusAction ()
    {
        $clusterStatus = $this->sendApiMethod('clusterGetServerStatus');
        if (! $clusterStatus)
            return;
        $nodeCount = $clusterStatus->responseData->serverList->count();
        $nodeError = 0;
        foreach ($clusterStatus->responseData->serversList->serverInfo as $serverInfo) {
            if ((string) $serverInfo->status != 'OK')
                $nodeError ++;
        }
        $errorRate = 0;
        if ($nodeCount != 0)
            $errorRate = ($nodeError / $nodeCount) * 100;
        $threshold = $this->getNagiosThresholdConfig();
        ksort($threshold);
        $severity = self::NAGIOS_OK;
        foreach ($threshold as $rateLimit => $nagiosSeverityCode) {
            if ($errorRate > $rateLimit)
                $severity = constant('self::' . $nagiosSeverityCode);
        }
        $this->setStatus($severity);
        $this->setStatusMessage(number_format($errorRate, 2) . '% of nodes are down');
    }
    
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
    							'from' => $this->getLastNodeTouch(),
    							'to' => time()
    					)
    			)
    	);
    	$this->nodeTouch();
    	if (! $auditTrail) return;
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Check for notifications
     */
    public function notificationsAction ()
    {
        $notifications = $this->sendApiMethod('getNotifications');
        if (! $notifications)
            return;
        $notificationsCount = $notifications->responseData->notifications->count();
        if ($notificationsCount == 0) {
            $this->setStatus(self::NAGIOS_OK);
            $this->setStatusMessage('No notifications');
            return;
        }
        $statusMessage = 'No notifications';
        $threshold = $this->getNagiosThresholdConfig();
        $severity = self::NAGIOS_OK;
        foreach ($notifications->responseData->notifications->notification as $notification) {
            $statusMessage .= (string) $notification->title . "\n";
            $notificationSeverity = (string) $notification->severity;
            $nagiosSeverity = constant(
                    'self::' . $threshold[$notificationSeverity]);
            if ($nagiosSeverity > $severity)
                $severity = $nagiosSeverity;
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }

    

    /**
     * Check validity and expiration license
     */
    public function licenseAction ()
    {
        $notifications = $this->sendApiMethod('getNotifications');
        if (! $notifications)
            return;
        $notificationsCount = $notifications->responseData->notifications->count();
        $statusMessage = 'License Ok';
        if ($notificationsCount == 0) {
            $this->setStatus(self::NAGIOS_OK);
            $this->setStatusMessage($statusMessage);
            return;
        }
        $threshold = $this->getNagiosThresholdConfig();
        $severity = self::NAGIOS_OK;
        $licenseTypeNotification = array(
                'TYPE_LICENSE_INVALID' => 26,
                'TYPE_LICENSE_ABOUT_TO_EXPIRE' => 27,
                'TYPE_LICENSE_ABOUT_TO_EXPIRE_45' => 30,
                'TYPE_LICENSE_ABOUT_TO_EXPIRE_15' => 31
        );
        $nagiosSeverity = self::NAGIOS_OK;
        foreach ($notifications->responseData->notifications->notification as $notification) {
            $type = (int) $notification->type;
            if (! in_array($type, $licenseTypeNotification))
                continue;
            $typeKey = current(array_keys($licenseTypeNotification, $type));
            $severity = constant('self::' . $threshold[$typeKey]);
            $statusMessage = (string) $notification->title;
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }

    /**
     * Check within the given interval time if any events trail have occurred.
     * Nagios severity level depends on the events severity type.
     */
    public function eventsAction ()
    {
        $events = $this->sendApiMethod('monitorGetIssuesByPredefinedFilter', 
                array(
                        'filterId' => 'All',
                        'limit' => $this->params('limit', 5),
                        'direction' => 'DESC'
                ));
        if (! $events)
            return;
        $delay = $this->params('delay', 10);
        $statusMessage = 'No event during last ' . $delay . ' second(s)';
        $severity = self::NAGIOS_OK;
        $threshold = $this->getNagiosThresholdConfig();
        foreach ($events->responseData->issues->issue as $issue) {
            $lastOccurance = (string) $issue->lastOccurance;
            $issueSeverity = constant(
                    'self::' . $threshold[(string) $issue->severity]);
            $rule = (string) $issue->rule;
            if (strtotime($lastOccurance) < time() - $delay)
                continue;
            $statusMessage .= $rule . " - ";
            $statusMessage .= $lastOccurance;
            $statusMessage .= "\n";
            if ($issueSeverity > $severity)
                $severity = $issueSeverity;
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}