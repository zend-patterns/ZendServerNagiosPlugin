<?php
namespace ZendServerNagiosPlugin\Controller;

/**
 * Nagios plugins controller dedicated to cluster-wide probes
 */

class ClusterstatusController extends AbstractNagiosController 
{
    /**
     * Check how many nodes are up in the cluster
     */
    public function clusterstatusAction()
    {
        $clusterStatus = $this->sendApiMethod('clusterGetServerStatus');
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
}