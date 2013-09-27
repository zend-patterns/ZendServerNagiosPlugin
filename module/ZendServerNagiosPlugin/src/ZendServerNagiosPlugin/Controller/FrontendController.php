<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use Zend\Console\ColorInterface;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Select;
use Zend\Config\Reader\Ini;
use Zend\Config\Factory;
/**
 * Nagios plugins controller
 */
class FrontendController extends AbstractNagiosController
{
    /**
     * Install the plugin
     * 
     * Set up configuration with web API key
     */
    public function installAction()
    {
        $this->setNrpeConfig();
        return true;
    }
    
    /**
     * Add Zedn Server Command to nrpe.cfg
     * 
     * And restart nrpe server.
     */
    protected function setNrpeConfig()
    {
        $this->consoleWrite('Setting nrpe config...');
        $nagiosConfig = $this->getNagiosConfig();
        $commandString = '#Zend Server plugin commands' . "\n";
        foreach ($this->getCommandRoutes() as $route => $config){
            $commandName = $config['options']['route'];
            $commandArray = explode(' ', $commandName);
            if (trim($commandArray[0]) != 'nagiosplugin') continue;
            $commandName = trim($commandArray[1]);
            if ($commandName == 'install') continue;
            $commandName = preg_replace('@\[[^[]]*\]@', '', $commandName);
            $commandLine  = 'command[zs-' . $commandName . ']=';
            $commandLine .= $nagiosConfig['plugin']['directory'];
            $commandLine .= '/index.php nagiosplugin ' . $commandName;
            $commandString .= $commandLine ."\n";
        }
        $nrepConfigFile = $nagiosConfig['client']['config']['directory'] . '/nrpe.cfg';
        file_put_contents($nrepConfigFile, $commandString,FILE_APPEND);
        exec('service nagios-nrpe-server restart');
        $this->consoleWriteLn('[OK]', ColorInterface::GREEN);
    }
}