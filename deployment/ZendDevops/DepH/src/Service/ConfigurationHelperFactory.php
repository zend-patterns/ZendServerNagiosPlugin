<?php
namespace ZendDevops\DepH\Service;

/**
 * Zend Server Configuration helper factory
 */
use ZendDevops\DepH\Configuration as Configuration;
use ZendDevops\DepH\Deployment\Context;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationHelperFactory implements \Zend\ServiceManager\FactoryInterface
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
            default : $helper = new Configuration\Apache\ConfigurationHelper();
        }
        $helper->setServiceManager($ServiceLocator);
        return $helper;
    }
}