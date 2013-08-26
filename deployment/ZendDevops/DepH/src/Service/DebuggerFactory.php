<?php
namespace ZendDevops\DepH\Service;

/**
 * Debugger factory
 */
use ZendDevops\DepH\Debugger\Session;
use Zend\ServiceManager\ServiceLocatorInterface;

class DebuggerFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $ServiceLocator            
     * @return \Zx\DepH\ZendServer\Context
     */
    public function createService (ServiceLocatorInterface $ServiceLocator)
    {
        $config = $ServiceLocator->get('config');
        if (array_key_exists('debugger', $config)){
            $session = new Session($config['debugger']);
            return $session;
        }
    }
}