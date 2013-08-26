<?php
/**
 * DepH - Zend Server Deployment Helper
 */

namespace ZendDevops\DepH\Log;

interface LogAwareInterface {
    /**
     * @param Log $log
     */
    public function setLog(Log $log);
} 