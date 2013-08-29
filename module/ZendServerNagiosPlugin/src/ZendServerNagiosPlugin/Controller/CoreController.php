<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use Zend\Console\ColorInterface;
/**
 * Nagios plugins controller
 */
class CoreController extends AbstractNagiosController
{
    /**
     * Install the plugin
     * 
     * Set up configuration with web API key
     */
    public function installAction()
    {
        //Set up web api key.
        $this->consoleWrite('Setting Web API key...');
        $debugTrace = debug_backtrace();
        $debugTrace = current(array_reverse($debugTrace));
        $indexFile =  $debugTrace['file'];
        $configFile = dirname($indexFile) .DIRECTORY_SEPARATOR . 'vendor';
        $configFile .= DIRECTORY_SEPARATOR . 'ZendServerWebApi';
        $configFile .= DIRECTORY_SEPARATOR . 'config';
        $configFile .= DIRECTORY_SEPARATOR . 'zendserverwebapi.config.php';
        $config = include $configFile;
        if ( ! array_key_exists('zsapi', $config)) $config['zsapi'] = array();
        $config['zsapi']['default_target'] =array(
           'zsurl' => $this->params('zsurl','http://localhost:10081')  ,
           'zskey' => $this->params('zskey','admin'),
           'zssecret' => $this->params('zssecret', '--no-key--'),
                   
        );
        $configWritter = new PhpArray();
        $configWritter->toFile($configFile, new Config($config));
        $this->consoleWriteLn('[OK]', ColorInterface::GREEN);
        
        //Set up Nagios file
        
        return false;
    }
}