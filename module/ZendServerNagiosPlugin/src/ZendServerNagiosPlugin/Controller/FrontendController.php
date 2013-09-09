<?php
namespace ZendServerNagiosPlugin\Controller;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use Zend\Console\ColorInterface;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Select;
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
        //Set up web api key.
        $zsurl = Line::prompt('Zend Server url : ',false,100);
        $zskey = Line::prompt('Zend Server web API key name : ',false,100);
        $zssecret = Line::prompt('Zend Server web API key secret : ',false,100);
        $zsversion = Select::prompt('Zend Server version : ',
                array(
                    '0' => '5.1',
                    '1' => '5.5',
                    '2' => '5.6',
                    '3' => '6.0',
                    '4' => '6.1'
                ),
                false,false
       );
        $config = new Config(array(
                'zsapi' => array(
                    'default_target' => array(
                        'zsurl' => $zsurl,
                        'zskey' => $zskey,
                        'zssecret' => $zssecret,
                        'zsversion' => $zsversion,     
                    ),
                ),
        ));
        $this->consoleWrite('Setting Web API key...');
        $configWritter = new PhpArray();
        $localConfigFile = __DIR__ .'/../../../../../config/autoload/zsapi.local.php';
        $configWritter->toFile($localConfigFile, $config);
        $this->consoleWriteLn('[OK]', ColorInterface::GREEN);
        
        //Set up Nagios file - nrpe.cfg
        
        
        return false;
    }
}