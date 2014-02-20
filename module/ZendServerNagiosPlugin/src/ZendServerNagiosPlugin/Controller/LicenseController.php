<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller
 */
class LicenseController extends AbstractNagiosController 
{
	protected static $licenseTypeNotification = array(
                'TYPE_LICENSE_INVALID' => 26,
                'TYPE_LICENSE_ABOUT_TO_EXPIRE' => 27,
                'TYPE_LICENSE_ABOUT_TO_EXPIRE_45' => 30,
                'TYPE_LICENSE_ABOUT_TO_EXPIRE_15' => 31
        );
	
    /**
     * Check validity and expiration license
     */
    public function licenseAction ()
    {
        $notifications = $this->sendApiMethod('getNotifications');
        $notificationsCount = $notifications->responseData->notifications->count();
        $statusMessage = 'License Ok';
        if ($notificationsCount == 0) {
            $this->setStatus(self::NAGIOS_OK);
            $this->setStatusMessage($statusMessage);
            return;
        }
        $threshold = $this->getNagiosThresholdConfig();
        $severity = self::NAGIOS_OK;
        $nagiosSeverity = self::NAGIOS_OK;
        foreach ($notifications->responseData->notifications->notification as $notification) {
            $creationTime = (int) $notification->creationTime;
            if ($this->getLastTouch()->getLastTouch() > $creationTime) continue;
            $type = (int) $notification->type;
            if (! in_array($type, self::$licenseTypeNotification)) continue;
            $typeKey = current(array_keys($licenseTypeNotification, $type));
            $severity = constant('self::' . $threshold[$typeKey]);
            $statusMessage = (string) $notification->title;
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}