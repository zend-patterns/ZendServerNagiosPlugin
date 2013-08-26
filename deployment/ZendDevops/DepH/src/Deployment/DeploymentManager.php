<?php
namespace ZendDevops\DepH\Deployment;

/**
 * Deployment Manager class
 * 
 * It is basicaly a serviceManager
 */
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Config;

class DeploymentManager extends \Zend\ServiceManager\ServiceManager
{
    /**
     * Ramp up the system...
     * 
     * @param string $currentActionScript
     */
    public function __construct($localConfigFile = null) {
        //Set service locator
        $serviceLocatorConfig = include(__DIR__ . '/../../config/servicemanager.config.php');
        $localConfig = array();
        if ($localConfigFile && is_readable($localConfigFile)){
            $localConfig = include ($localConfigFile);
            if (array_key_exists('service_locator',$localConfig))
                $serviceLocatorConfig = array_merge($serviceLocatorConfig, $localConfig['service_locator']);
        }
        //Set global configuration
        $config = $localConfig;
        $config['service_locator'] = $serviceLocatorConfig;
        $this->setService('config', $config);
        //EventManager
        $this->setInvokableClass('EventManager', '\Zend\EventManager\EventManager');
        //Database
        if (array_key_exists('db', $config))
            $this->setFactory('db', 'ZendDevops\DepH\Service\DbFactory');
        //The end...
        parent::__construct(new Config($serviceLocatorConfig));
        return;
    }
    
    /**
     * Shortcut for starting the Log
     * @return \Zx\DepH\Log\Log
     */
    public function startLog() {
        return $this->get('Log');
    }
    
    /**
     * Shortcut for starting the log with GUI Output
     * @return \Zx\DepH\Log\Log
     */
    public function startGuiLog() {
        $log = $this->get('Log');
        $log->addGuiOutput();
        return $log;
    }
    
    /**
     * @return \Zx\DepH\Db\AdapterMysqli
     */
    public function getDb() {
        return $this->get('DB');
    }
    
    /**
     * @return \Zx\DepH\Debugger\ZendDebugger
     */
    public function getDebugger() {
        return $this->get('ZendDebugger');
    }
    
    /**
     * @return \Zx\DepH\Deployment\Deployment
     */
    public function getDeployment() {
        return $this->get('Deployment');
    }
    
    /**
     * @return \Zx\DepH\File\Template
     */
    public function getTemplate() {
        return $this->get('Template');
    }
    
    /**
     * @return \Zx\DepH\Params\Params
     */
    public function getParams() {
        return $this->get('Params');
    }
    
    /**
     * @return \Zx\DepH\Path\Path
     */
    public function getPath() {
        return $this->get('Path');
    }
    
    /**
     * @return \Zx\DepH\SystemCall\Shell
     */
    public function getShell() {
        return $this->get('Shell');
    }
    
    /**
     * @return \Zx\DepH\Vhost\Vhost
     */
    public function getVhost() {
        return $this->get('Vhost');
    }    
}