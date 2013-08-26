<?php
namespace ZendDevops\DepH\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDevops\DepH\Log\Log;

class LogFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $ServiceLocator            
     * @return \Zx\DepH\ZendServer\Context
     */
    public function createService (ServiceLocatorInterface $ServiceLocator)
    {
        $context = $ServiceLocator->get('deployment_context');
        $filter = new \Zend\I18n\Filter\Alnum();
        $appName = $filter->filter($context->getApplicationName());
        $appVersion = $context->getCurrentAppVersion();
        $logFilePath = $context->getServerDirectory() . 'logs' . DIRECTORY_SEPARATOR . 'deployment' . DIRECTORY_SEPARATOR;
        if ( ! is_readable($logFilePath)) mkdir($logFilePath, '0777' , true);
        $logFilePath .= $appName . '_' . $appVersion .'.log';
        $writer = new \Zend\Log\Writer\Stream($logFilePath);
        $logger = new Log();
        $logger->addWriter($writer);
        $logger->setFullVerboseExcludeList(array(
                '/Zx/',
                '/ZendFramework2/',
                'deph.php'
        ));
        $logger->attach($ServiceLocator->get('eventManager'));
        return $logger;
    }
}