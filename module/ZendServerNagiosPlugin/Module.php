<?php
namespace ZendServerNagiosPlugin;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, 
        ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\ConfigProviderInterface::getConfig()
     */
    public function getConfig ()
    {
        return include __DIR__ . '/config/zendservernagiosplugin.config.php';
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     */
    public function getAutoloaderConfig ()
    {
        return array(
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__ => __DIR__ . '/src/' .
                                         __NAMESPACE__
                        )
                )
        );
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\ConsoleBannerProviderInterface::getConsoleBanner()
     */
    public function getConsoleBanner (AdapterInterface $console)
    {
        return "---- ZSNagios Plugin ------\n";
    }

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage (AdapterInterface $console)
    {
        $config = $this->getConfig();
        $routes = $config['console']['router']['routes'];
        $return = array();
        foreach ($routes as $route) {
            $return[$route['options']['route']] = $route['options']['usage'];
        }
        return $return;
    }
}
