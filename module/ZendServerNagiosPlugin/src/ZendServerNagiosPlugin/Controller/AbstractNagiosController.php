<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use ZendServerWebApi\Model\Exception\ApiException;

abstract class AbstractNagiosController extends AbstractActionController
{
    const NAGIOS_OK = 0;
    const NAGIOS_WARNING = 1;
    const NAGIOS_CRITICAL = 2;
    const NAGIOS_UNKNOWN = 3;
    
    /**
     * Nagios plugin status
     * @var int
     */
    protected $status = self::NAGIOS_UNKNOWN;
    
    /**
     * Nagios plugin message
     * @var string
     */
    protected $statusMessage = 'Service unreachable';
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(MvcEvent $e)
    {
    	$actionResponse = parent::onDispatch($e);
        fwrite(STDOUT, $this->statusMessage);
        exit($this->status);
    }
    
    /**
     * Set the plugin status
     * @param unknown $status
     */
    protected function setStatus($status)
    {
        $availableStatus = array(self::NAGIOS_CRITICAL, self::NAGIOS_OK, self::NAGIOS_UNKNOWN, self::NAGIOS_WARNING);
        if (! in_array($status, $availableStatus)) $this->status = self::NAGIOS_UNKNOWN;
        else $this->status = $status;
    }
	/**
	 * @param string $statusMessage
	 */
	protected function setStatusMessage($statusMessage) {
		$this->statusMessage = $statusMessage;
	}
	

	/**
	 * Return the Nagios thresholds configuration for the current action
	 * @return NULL|unknown
	 */
	protected function getNagiosThresholdConfig()
	{
		$config = $this->getServiceLocator()->get('config');
		$nagiosConfig = $config['nagios_threshold'];
		if ( ! is_array($nagiosConfig[$this->params('action')])) return null;
		return  $nagiosConfig[$this->params('action')];
	}
	
	/**
	 * Send API Method and manage API exception
	 * 
	 * Return true if the request succed.
	 * @param string $method
	 * @return bool| ApiResponse
	 */
	protected function sendApiMethod($method, $params = array()) 
	{
	    $serviceManager = $this->getServiceLocator();
	    $apiManager = $serviceManager->get('zend_server_api');
	    try {
	       $methodResponse = $apiManager->$method($params);
	    } catch (ApiException $e)
	    {
	       $this->setStatusMessage('[ZendServerAPIError] - ' . $e->getApiErrorCode());
	       $this->setStatus(self::NAGIOS_CRITICAL);
	       return false;
	    }
	    return $methodResponse;
	}
	
	/**
	 * Write in console
	 * @param string $text
	 * @param string $color
	 * @param string $bgcolor
	 */
	protected function consoleWrite($text,$color = null, $bgcolor= null)
	{
	    $console = $this->getServiceLocator()->get('console');
	    $console->write($text,$color = null, $bgcolor= null);
	}
	/**
	 * Write in console
	 * @param string $text
	 * @param string $color
	 * @param string $bgcolor
	 */
	protected function consoleWriteLn($text,$color = null, $bgcolor= null)
	{
	    $console = $this->getServiceLocator()->get('console');
	    $console->writeLine($text,$color = null, $bgcolor= null);
	}

}