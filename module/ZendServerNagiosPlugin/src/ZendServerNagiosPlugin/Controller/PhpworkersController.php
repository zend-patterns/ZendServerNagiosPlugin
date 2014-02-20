<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class PhpworkersController extends AbstractNagiosController 
{
    /**
     * Check for failed jobs
     */
    public function PhpworkersAction ()
    {
        $phpworkers = $this->sendApiMethod('statisticsGetSeries', array(
        	'from' => $this->getLastTouch()->getLastTouch() - 24 * 3600,
    		'to' => time(),
        	'type' => 'AVG_REQUEST_PROCESSING_TIME'
        ));
        $this->setStatus(self::NAGIOS_OK);
        $statusMessage = '';
        $threshold = $this->getNagiosThresholdConfig('phpworkers');
        $severity = self::NAGIOS_OK;
        foreach ($threshold as $nbJobs => $severity)
        {
        	if ($total > $nbJobs) $severity = constant('self::' . $threshold[$nbJobs]);
        }
        foreach ($phpworkers->responseData->jobs->job as $job) {
            $statusMessage .= (string) $job->name . ' : ';
            $statusMessage .= (string) $job->script . "  |  ";
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}