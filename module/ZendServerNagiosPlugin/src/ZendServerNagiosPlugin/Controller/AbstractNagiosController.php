<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use ZendServerWebApi\Model\Exception\ApiException;
use ZendServerNagiosPlugin\Model\Touch;
use ZendServerWebApi\Model\ApiManager;
use ZendServerWebApi\Model\Http\Client;

abstract class AbstractNagiosController extends AbstractActionController
{
    const NAGIOS_OK = 0;
    const NAGIOS_WARNING = 1;
    const NAGIOS_CRITICAL = 2;
    const NAGIOS_UNKNOWN = 3;
    
    const NODE_ONLY_MODE = 10;
    const CLUSTER_MODE = 20;
    
    /**
     * Nagios plugin status
     * @var int
     */
    protected $status = self::NAGIOS_UNKNOWN;
    
    /**
     * Nagios plugin message
     * @var string
     */
    protected $statusMessage = '';
    
    /**
     * Current Nagios requesting mode (node / cluster)
     * 
     * @var int
     */
    protected $requestingMode = self::CLUSTER_MODE;
    
    /**
     * Node Id (id of the node in the database)
     * 
     * @var int
     */
    protected $nodeId = -1;
    
    /**
     * Footprint of the current response
     * 
     * @var string
     */
    protected $footprint = '';
    
    /**
     * Is the foot print has change
     * 
     * @var boolean
     */
    protected $hasChange = false;
    
	/**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(MvcEvent $e)
    {
    	$actionResponse = parent::onDispatch($e);
        fwrite(STDOUT, $this->statusMessage . "\n");
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
	 * Set requesting mode  : cluster
	 */
	protected function setRequestingModeCluster()
	{
		$this->requestingMode = self::CLUSTER_MODE;
	}
	
	/**
	 * Set requesting mode  : node only
	 */
	protected function setRequestingModeNode()
	{
		$this->requestingMode = self::NODE_ONLY_MODE;
	}
	
	/**
	 * Return the Nagios thresholds configuration for the current action
	 * @return NULL|unknown
	 */
	protected function getNagiosThresholdConfig($action = null)
	{
		$config = $this->getServiceLocator()->get('config');
		$nagiosConfig = $config['nagios_threshold'];
		if ( ! $action) $action = $this->params('action');
		if ( ! is_array($nagiosConfig[$action])) return null;
		return  $nagiosConfig[$action];
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
	    $config = $this->getServiceLocator()->get('config');
	    $serviceManager = $this->getServiceLocator();
	    $target = current($serviceManager->get('target_manager'))->getTarget('default');
	    $apiMethodConfig = $serviceManager->get('apiMethodsConfig');
	    $apiManager = new ApiManager();
        $apiManager->setTarget($target);
        $apiManager->setApiMethodsConfig($apiMethodConfig);
        $httpClientConfig = $config['api_http_client'];
        $zsclient = new $httpClientConfig['class']('',$httpClientConfig['config']);
        $apiManager->setZendServerClient($zsclient);
	    try {
	       $methodResponse = $apiManager->$method($params);
	       $this->footprint = Touch::computeFootprint($methodResponse->getHttpResponse()->getBody());
	       $lastFootprint = $this->getLastTouch()->getFootPrint();
	       if ($this->footprint != $lastFootprint) $this->hasChnage = true;
	       else $this->hasChnage = false;
	       $this->touch($this->footprint);
	    } catch (ApiException $e) {
	       $this->setStatusMessage('[ZendServerAPIError] - ' . $e->getApiErrorCode());
	       $this->setStatus(self::NAGIOS_CRITICAL);
	       if (isset($methodResponse)) return $methodResponse;
	       throw $e;
	    } catch (\Exception $e) {
	        throw $e;
	    }
	    return $methodResponse;
	}
	
	/**
	 * Write in console
	 * 
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
	
	/**
	 * Getting console object
	 * @return Ambigous <object, multitype:, stdClass>
	 */
	protected function getConsole()
	{
	    return $this->getServiceLocator()->get('console');
	}
	
	/**
	 * Get Nagios client plugin configuration
	 * @return array
	 */
	protected function getNagiosConfig()
	{
	    $config =  $this->getServiceLocator()->get('config');
	    return $config['nagios'];
	}
	
	/**
	 * Get Nagios calss buffer manager
	 * @return NagiosCallsBufferManager
	 */
	protected function getNagiosCallsBufferManager()
	{
		return $this->getServiceLocator()->get('NagiosCallsBufferManager');
	}
	
	/**
	 * Return command routes
	 * 
	 * @return array
	 */
	protected function getCommandRoutes()
	{
	    $config = $this->getServiceLocator()->get('config');
	    $commandRoutes = $config['console']['router']['routes'];
	    return $commandRoutes;
	}
	
	/**
	 * Set the last time the node has been touch by Nagios for the current command
	 */
	protected function touch($footprint = '')
	{
		$nodeId = 0;
		if ($this->requestingMode != self::CLUSTER_MODE) $nodeId = $this->getNodeId();
		$command = $this->getEvent()->getRouteMatch()->getParam('action');
		$this->getNagiosCallsBufferManager()->nodeTouch($command, $nodeId, $footprint);
	}
	
	/**
	 * Return the last touch
	 * 
	 * @var Touch
	 */
	protected function getLastTouch()
	{
		$command = $this->getEvent()->getRouteMatch()->getParam('action');
		if ($this->requestingMode == self::NODE_ONLY_MODE) 
			return $this->getNagiosCallsBufferManager()->getLastNodeTouch($command, $this->getNodeid());
		if ($this->requestingMode == self::CLUSTER_MODE) 
			return $this->getNagiosCallsBufferManager()->getLastClusterTouch($command); 
	}
	
	/**
	 * Return the hasChnage value.
	 * 
	 * @return bololean
	 */
	protected function hasChange()
	{
		return $this->hasChnage;
	}
	
	/**
	 * Return the url of the target Zend Server
	 * 
	 * @return string
	 */
	protected function getZendServerUrl()
	{
		$config = $this->getServiceLocator()->get('config');
		return $config['zsapi']['target']['zsurl'];
	}
	
	/*
	 * Return node Id
	 * 
	 * @retun int 
	 */
	protected function getNodeId()
	{
		if ($this->nodeId > -1) return $this->nodeId;
		$clusterStatus = $this->sendApiMethod('clusterGetServerStatus');
		if ( ! $clusterStatus) return false;
		$currentHost = gethostname();
		foreach ($clusterStatus->responseData->serversList->serverInfo as $serverInfo){
			$serverId = (string)$serverInfo->id;
			$name = (string)$serverInfo->name;
			if ($currentHost == $name) {
				$this->nodeId = $serverId;
				return $serverId;
			}
		}
	}
	
	/**
	 * Return nagios  severity
	 * 
	 * @param mixed $value
	 * @param string $action
	 * @return int
	 */
	protected function getNagiosSeverity($value,$action = null)
	{
		$config = $this->getNagiosThresholdConfig($action);
		asort($config);
		foreach ($config as $threshold => $severyString)
		{
			if (is_int($value)) {
				if ($value >= $threshold) return constant('self::' . $severyString);
			}
			else if ($value == $threshold) return constant('self::' . $severyString);
			
		}
		return self::NAGIOS_OK;
	}
}