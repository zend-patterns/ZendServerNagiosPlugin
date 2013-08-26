<?php
namespace ZendDevops\DepH\Service;

/**
 * Zend Server Context factory
 */
use ZendDevops\DepH\Deployment\Context;
use ZendDevops\DepH\Deployment\Apache as Apache;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContextFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $ServiceLocator            
     * @return \Zx\DepH\ZendServer\Context
     */
    public function createService (ServiceLocatorInterface $ServiceLocator)
    {
        $config = $ServiceLocator->get('config');
        switch(getenv('ZS_WEBSERVER_TYPE')) {
            default : $context = new Apache\Context();
        }
        if (array_key_exists('deployment_context', $config)){
            $context->setConfig($config['deployment_context']);
        }
        return $context;
    }
}