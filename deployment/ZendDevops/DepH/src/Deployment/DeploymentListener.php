<?php
/**
 * Main depoyment class
 * 
 * It is an listener aggregate that listen to the Deployment event.
 * It is connected to the main service locator (Deployment Manager) 
 *
 */
namespace ZendDevops\DepH\Deployment;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\Event;


class DeploymentListener implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    /**
     * Deployment steps
     */
    const STEP_PRE_STAGE = 'pre_stage';
    const STEP_POST_STAGE = 'post_stage';
    const STEP_PRE_ACTIVATE = 'pre_activate';
    const STEP_POST_ACTIVATE = 'post_activate';
    const STEP_PRE_DEACTIVATE = 'pre_deactivate';
    const STEP_POST_DEACTIVATE = 'post_deactivate';
    const STEP_PRE_UNSTAGE = 'pre_unstage';
    const STEP_POST_UNSTAGE = 'post_unstage';
    
    /**
     * Deployment Manager
     * 
     * Service Locator that contains all what it needs to maange deployment processes
     * @var Zx\DepH\Deployment\DeploymentManager
     */
    protected $deploymentManager;

    /**
     * Check if the operations are running
     * @var boolean
     */
    protected $isRunning = false;
    
    /**
     * Return the current deployment step
     *
     * The value is parsed from the current hook script name (ie : pre_stage.php)
     * @return string
     */
    public function getStep()
    {
        $tmp = explode(DIRECTORY_SEPARATOR,$this->getScriptRequest());
        $tmp = array_reverse($tmp);
        $scriptName = basename($tmp[0],'.php');
        return constant('self::STEP_' . strtoupper($scriptName));
    }
    
    /**
     * Return the current request
     */
    public function  getScriptRequest()
    {
        $debugTrace = debug_backtrace();
        $debugTrace = current(array_reverse($debugTrace));
        return $debugTrace['file'];
    }
    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
        if ( ! is_a($serviceLocator, 'ZendDevops\DepH\Deployment\DeploymentManager')) 
            throw new \Exception('The given service locator is not a deployment manager');
        $this->deploymentManager = $serviceLocator;
    }
    
    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
    */
    public function getServiceLocator()
    {
        return $this->deploymentManager;
    }
    
    /**
     * Constrcuctor
     * @param string $localConfigFile
     */
    public function __construct($localConfigFile = null)
    {
        $deploymentManager =  new DeploymentManager($localConfigFile);
        $this->setServiceLocator($deploymentManager);
        $eventManager = $this->getServiceLocator()->get('eventManager');
        $this->attach($eventManager);
        $this->getLog();
    }
    
    /**
     * Chek if a debug session is running
     */
    public function isDebug()
    {
       return (bool)preg_match('@debug_session_id@', $_SERVER['QUERY_STRING']);
    }
    
    /**
     * Set Debug mode and relaunch script
     * @throws Exception\RuntimeException
     */
    public function debug()
    {
        if ($this->isDebug()) return;
        $debugSession = $this->getServiceLocator()->get('debugger');
        $debugSession->setBaseScript($this->getScriptRequest());
        $debugSession->launch();
    }
    
    /**
     * Run the process
     */
    public function run()
    {
        ini_set('display_errors', 0);
        $step = $this->getStep();
        //Manage debugging
        try {
            $this->isRunning = true;
            $results = $this->getServiceLocator()
                ->get('eventManager')
                ->trigger($step);
            if ( ! is_bool($results->last())) $isOk = false;
            else $isOk = $results->last();
            if( $results->last() === null) $isOk = true;
        }
        catch (\Exception $exception) {
            $message = $exception->getMessage();
            $message .= $exception->getTraceAsString();
            $this->getLog()->err($message);
            $isOk = false;
        }
        if ( $isOk) {
            $this->getLog()->notice($step . ' succeed');
            exit(0);
        }
        else {
            $this->getLog()->err($step . ' failed');
            exit(1);
        }
    }
    
    /**
     * Attach the hooks to the deployment event
     * 
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $eventManager) {
        $eventManager->attach(self::STEP_PRE_STAGE,array($this,'preStageAction'),1);
        $eventManager->attach(self::STEP_POST_STAGE,array($this,'postStageAction'),1);
        $eventManager->attach(self::STEP_PRE_ACTIVATE,array($this,'preActivateAction'),1);
        $eventManager->attach(self::STEP_POST_ACTIVATE,array($this,'postActivateAction'),1);
        $eventManager->attach(self::STEP_PRE_DEACTIVATE,array($this,'preDeactivateAction'),1);
        $eventManager->attach(self::STEP_POST_DEACTIVATE,array($this,'postDeactivateAction'),1);
        $eventManager->attach(self::STEP_PRE_UNSTAGE,array($this,'preUnstageAction'),1);
        $eventManager->attach(self::STEP_POST_UNSTAGE,array($this,'postUnstageAction'),1);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\ListenerAggregateInterface::detach()
     */
    public function detach(EventManagerInterface $eventManager) {
    
    }
    
    /**
     * Default deployment step listener
     * 
     * These functions return true if the operation are ok, false otherwise.
     * @param Event $event
     * @return bool
     */
    public function preStageAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }
    
    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function postStageAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }

    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function preActivateAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }

    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function postActivateAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }

    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function preDeactivateAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }

    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function postDeactivateAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }

    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function preUnstageAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }

    /**
     * (no-doc)
     * @see preStageAction()
     */
    public function postUnstageAction(Event $event)
    {
        $this->getLog()->notice('No hook defined for ' . $event->getName() .' step.');
        return true;
    }
    
    /**
     * Get the logger object
     * @return Zx\DepH\Log\Log
     */
    public function getLog()
    {
        return $this->getServiceLocator()->get('log');
    }
    
    /**
     * get the DB adapter
     * @return Zedn\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        if ( ! $this->getServiceLocator()->has('db')) return null;
        return $this->getServiceLocator()->get('db');
    }
    
    /**
     * Return the deployment context
     * 
     * @return Zx\DepH\Deployment\Context
     */
    public function getContext()
    {
        return $this->getServiceLocator()->get('deployment_context');
    }
    
    /**
     * Return the configuration helper
     */
    public function getConfigurationHelper()
    {
        return $this->getServiceLocator()->get('configuration_helper');
    }
    
    /**
     * 
     * @return Ambigous <object, multitype:, template_manager>
     */
    public function getTemplateManager()
    {
        return $this->getServiceLocator()->get('template_manager');
    }
}