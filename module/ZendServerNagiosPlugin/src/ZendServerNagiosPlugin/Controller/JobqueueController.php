<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class JobqueueController extends AbstractNagiosController 
{
    /**
     * Check for failed jobs
     */
    public function jobqueueAction ()
    {
        $jobqueue = $this->sendApiMethod('jobqueueJobsList', array(
        	'filter' => array(
        		'status' => 'Failed',
        		'executed_after' => $this->getLastTouch()->getLastTouch(),
        	),
        ));
        $total = (int) $jobqueue->responseData->total;
        if ($total == 0) {
            $this->setStatus(self::NAGIOS_OK);
            $this->setStatusMessage('No failed job');
            return;
        }
        $statusMessage = $total . ' failed jobs : ';
        $threshold = $this->getNagiosThresholdConfig();
        $severity = self::NAGIOS_OK;
        foreach ($threshold as $nbJobs => $severity)
        {
        	$level = constant('self::' . $threshold[$nbJobs]);
        	if ($total > $nbJobs && $severity < $level) $severity = $level ;
        }
        foreach ($jobqueue->responseData->jobs->job as $job) {
            $statusMessage .= (string) $job->name . ' : ';
            $statusMessage .= (string) $job->script . "  |  ";
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}