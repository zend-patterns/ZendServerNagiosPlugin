<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class ProcessingtimeController extends AbstractNagiosController 
{
    /**
     * Check for failed jobs
     */
    public function processingtimeAction ()
    {
        $processingtime = $this->sendApiMethod('statisticsGetSeries', array(
        	'from' => $this->getLastTouch()->getLastTouch(),
    		'to' => time(),
        	'type' => 'AVG_REQUEST_PROCESSING_TIME'
        ));
        $this->setStatus(self::NAGIOS_OK);
        $statusMessage = '';
        $threshold = $this->getNagiosThresholdConfig('processingtime');
        $severity = self::NAGIOS_OK;
        foreach ($processingtime->responseData->series->data->i as $i) {
            $timeElapsed = (int)$i;
            $issueSeverity = $this->getNagiosSeverity($timeElapsed);
            $time = strftime('%F %T', (int)$i['ts'] / 1000);
            if ($issueSeverity > self::NAGIOS_OK) $statusMessage .= $timeElapsed . 'ms : ' . $time . ' | ';
            if ($issueSeverity > $severity) $severity = $issueSeverity;
        }
        if ($statusMessage == '') $statusMessage = 'No data';
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}