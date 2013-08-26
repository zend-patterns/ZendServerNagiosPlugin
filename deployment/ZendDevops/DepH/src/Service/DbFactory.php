<?php
namespace ZendDevops\DepH\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;

class DbFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $ServiceLocator            
     * @return \Zx\DepH\ZendServer\Context
     */
    public function createService (ServiceLocatorInterface $ServiceLocator)
    {
        $context = $ServiceLocator->get('deployment_context');
        $config = $ServiceLocator->get('config');
        if ( ! isset($config['db'])) return null;
        $databaseConfig = $config['db'];
        $dbCredentials = array('database','username','hostname','password');
        foreach ($dbCredentials as $credential)
        {
            $value =  $context->getParam($config['db'][$credential]);
            if ( ! $value) $value = $config['db'][$credential];
            $databaseConfig[$credential] = $value;
        }
        $adapter = new Adapter($databaseConfig);
        return $adapter;
    }
}