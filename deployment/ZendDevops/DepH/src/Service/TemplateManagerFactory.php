<?php
namespace ZendDevops\DepH\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDevops\DepH\Configuration\TemplateManager;

class TemplateManagerFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $ServiceLocator
     * @return \Zx\DepH\ZendServer\Context
     */
    public function createService (ServiceLocatorInterface $ServiceLocator)
    {
        $context = $ServiceLocator->get('deployment_context');
        $manager = new TemplateManager();
        $manager->setContext($context);
        return $manager;
    }
}