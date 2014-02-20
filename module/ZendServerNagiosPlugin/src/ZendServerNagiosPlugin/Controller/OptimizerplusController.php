<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to Audit Trail
 */

class OptimizerplusController extends AbstractNagiosController 
{
    /**
     * Check for failed jobs
     */
    public function OptimizerplusAction ()
    {
        $statOplus = $this->sendApiMethod('statisticsGetSeries', array(
        	'from' => $this->getLastTouch()->getLastTouch() - 48 * 3600,
    		'to' => time(),
        	'type' => 'OPLUS_MEMORY_WASTED'
        ));
        var_dump($statOplus->getHttpResponse()->getBody());
        die();
        $total = (int) $statOplus->responseData->total;
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
        	if ($total > $nbJobs) $severity = constant('self::' . $threshold[$nbJobs]);
        }
        foreach ($statOplus->responseData->jobs->job as $job) {
            $statusMessage .= (string) $job->name . ' : ';
            $statusMessage .= (string) $job->script . "  |  ";
        }
        $this->setStatusMessage($statusMessage);
        $this->setStatus($severity);
    }
}