<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class NotificationsController extends AbstractNagiosController 
{
    /**
     * Check for notifications
     */
    public function notificationsAction ()
    {
        $notifications = $this->sendApiMethod('getNotifications');
        $notificationsCount = $notifications->responseData->notifications->count();
        if ($notificationsCount == 0) {
            $this->setStatus(self::NAGIOS_OK);
            $this->setStatusMessage('No notifications');
            return;
        }
        $statusMessage = '';
        $threshold = $this->getNagiosThresholdConfig();
        $severity = self::NAGIOS_OK;
        foreach ($notifications->responseData->notifications->notification as $notification) {
            $creationTime = (int) $notification->creationTime;
            if ($this->getLastTouch()->getLastTouch() > $creationTime) continue;
            $statusMessage .= (string) $notification->title . "\n";
            $notificationSeverity = (string) $notification->severity;
            $nagiosSeverity = constant('self::' . $threshold[$notificationSeverity]);
            if ($nagiosSeverity > $severity)
                $severity = $nagiosSeverity;
        }
        if ($statusMessage == '' ) $statusMessage = 'No notifications';
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}